<?php

namespace Concrete\Core\Api\Controller;

use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\File\File;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\File\Import\ImportOptions;
use Concrete\Core\File\Importer;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\Pagination\PagerPagination;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\Api\ApiController;
use Concrete\Core\Api\Attribute\AttributeValueMapFactory;
use Concrete\Core\File\Search\ColumnSet\Column\FileVersionDateAddedColumn;
use Concrete\Core\Api\Fractal\Transformer\FileTransformer;
use Concrete\Core\Api\Resources;
use Concrete\Core\Api\Traits\SetListLimitFromQueryTrait;
use Concrete\Core\Api\Traits\SupportsCursorTrait;
use League\Fractal\Resource\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;

class Files extends ApiController
{

    use SetListLimitFromQueryTrait;
    use SupportsCursorTrait;

    /**
     * @OA\Get(
     *     path="/ccm/api/1.0/files/{fileID}",
     *     tags={"files"},
     *     summary="Find a file by its ID",
     *     security={
     *         {"authorization": {"files:read"}}
     *     },
     *     @OA\Parameter(
     *         name="fileID",
     *         in="path",
     *         description="ID of file to return",
     *         required=true
     *     ),
     *     @OA\Parameter(
     *         name="includes",
     *         in="query",
     *         explode=false,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="string", enum={"custom_attributes"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/File"),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="You do not have the proper permissions to access this resource."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="File not found"
     *     ),
     * )
     */
    public function read($fID)
    {
        $file = File::getByUUIDOrID($fID);
        if (!$file) {
            return $this->error(t('File not found.'), 404);
        } else {
            $permissions = new Checker($file);
            if (!$permissions->canViewFileInFileManager()) {
                return $this->error(t('You do not have access to read properties about this file.'), 401);
            }
        }

        return $this->transform($file, new FileTransformer(), Resources::RESOURCE_FILES);
    }

    /**
     * @OA\Get(
     *     path="/ccm/api/1.0/files",
     *     tags={"files"},
     *     summary="Returns a list of file objects, sorted by last updated descending. The most recent file objects appear first.",
     *     security={
     *         {"authorization": {"files:read"}}
     *     },
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="The number of objects to return. Must be 100 or less. Defaults to 10.",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="after",
     *         in="query",
     *         description="The ID of the current object to start at."
     *     ),
     *     @OA\Parameter(
     *         name="includes",
     *         in="query",
     *         explode=false,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="string", enum={"custom_attributes"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/File")
     *         ),
     *     ),
     * )
     */
    public function listFiles()
    {
        $list = new FileList();
        $list->setPermissionsChecker(
            function ($file) {
                $fp = new Checker($file);
                return $fp->canViewFileInFileManager();
            }
        );

        $fileVersionColumn = new FileVersionDateAddedColumn();
        $fileVersionColumn->setColumnSortDirection('desc');
        $this->setupSortAndCursor(
            $this->request,
            $list,
            $fileVersionColumn,
            function ($currentCursor) {
                $file = File::getByUUIDOrID($currentCursor);
                return $file;
            }
        );

        $pagination = new PagerPagination($list);
        $this->addLimitToPaginationIfSpecified($pagination, $this->request);

        $results = $pagination->getCurrentPageResults();
        $resource = new Collection($results, new FileTransformer(), Resources::RESOURCE_FILES);
        $this->addCursorToResource($results, $this->request, function($file) {
            if ($file->hasFileUUID()) {
                return $file->getFileUUID();
            } else {
                return $file->getFileID();
            }
        }, $resource);

        return $resource;
    }

    /**
     * @OA\Post(
     *     path="/ccm/api/1.0/files",
     *     tags={"files"},
     *     summary="Adds a file object.",
     *     security={
     *         {"authorization": {"files:add"}}
     *     },
     *     @OA\RequestBody(ref="#/components/requestBodies/NewFile"),
     *     @OA\Response(
     *         response=200,
     *         description="Successful files operation",
     *         @OA\JsonContent(ref="#/components/schemas/File"),
     *     ),
     * )
     */
    public function add()
    {
        $cf = $this->app->make('helper/file');
        $uploadedFile = $this->request->files->get('file');
        if ($post_max_size = $this->app->make('helper/number')->getBytes(ini_get('post_max_size'))) {
            if ($post_max_size < $_SERVER['CONTENT_LENGTH']) {
                return $this->error(Importer::getErrorMessage(Importer::E_FILE_EXCEEDS_POST_MAX_FILE_SIZE), 400);
            }
        }
        if (!$uploadedFile instanceof UploadedFile) {
            return $this->error(Importer::getErrorMessage(Importer::E_FILE_INVALID), 400);
        }
        if (!$uploadedFile->isValid()) {
            return $this->error(Importer::getErrorMessage($uploadedFile->getError()), 400);
        }

        $treeNodeID = $this->request->request->get('folder');
        if ($treeNodeID) {
            $treeNodeID = is_scalar($treeNodeID) ? (int) $treeNodeID : 0;
            $folder = $treeNodeID === 0 ? null : Node::getByID($treeNodeID);
        } else {
            $filesystem = new Filesystem();
            $folder = $filesystem->getRootFolder();
        }
        if (!$folder instanceof FileFolder) {
            return $this->error(t('Unable to find specified folder'), 400);
        }

        $fp = new Checker($folder);
        if (!$fp->canAddFileType($cf->getExtension($uploadedFile->getClientOriginalName()))) {
            return $this->error(Importer::getErrorMessage(Importer::E_FILE_INVALID_EXTENSION), 403);
        }

        /**
         * @var $importer FileImporter
         * @var $importOptions ImportOptions
         */
        $importer = $this->app->make(FileImporter::class);
        $importOptions = $this->app->make(ImportOptions::class);
        if ($folder) {
            $importOptions->setImportToFolder($folder);
        }
        $file = $importer->importLocalFile($uploadedFile->getPathname(), $uploadedFile->getClientOriginalName(), $importOptions);
        return $this->transform($file->getFile(), new FileTransformer(), Resources::RESOURCE_FILES);
    }

    /**
     * @OA\Delete(
     *     path="/ccm/api/1.0/files/{fileID}",
     *     tags={"files"},
     *     summary="Delete a file by its ID",
     *     security={
     *         {"authorization": {"files:delete"}}
     *     },
     *     @OA\Parameter(
     *         name="fileID",
     *         in="path",
     *         description="ID of file to delete",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/DeletedResponse"),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="You do not have the proper permissions to delete this resource."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="File not found"
     *     ),
     * )
     */
    public function delete($fID)
    {
        $file = File::getByUUIDOrID($fID);
        if (!$file) {
            return $this->error(t('File not found'), 404);
        }

        $checker = new Checker($file);
        if (!$checker->canDeleteFile()) {
            return $this->error(t('You do not have access to delete this file.', 401));
        }

        $file->delete();

        return $this->deleted(Resources::RESOURCE_FILES, $fID);
    }

    /**
     * @OA\Put(
     *     path="/ccm/api/1.0/files/{fileID}",
     *     tags={"files"},
     *     summary="Update a file by its ID",
     *     security={
     *         {"authorization": {"files:update"}}
     *     },
     *     @OA\Parameter(
     *         name="fileID",
     *         in="path",
     *         description="ID of file to update",
     *         required=true
     *     ),
     *     @OA\RequestBody(ref="#/components/requestBodies/UpdatedFile"),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/File"),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="You do not have the proper permissions to update this resource."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="File not found"
     *     ),
     * )
     */
    public function update($fID)
    {
        $file = File::getByUUIDOrID($fID);
        if (!$file) {
            return $this->error(t('File not found'), 404);
        }

        $checker = new Checker($file);
        if (!$checker->canEditFile()) {
            return $this->error(t('You do not have access to edit this file.', 401));
        }

        $body = json_decode($this->request->getContent(), true);
        $version = $file->getVersionToModify();
        if (isset($body['title'])) {
            $version->updateTitle($body['title']);
        }
        if (isset($body['description'])) {
            $version->updateDescription($body['description']);
        }
        if (isset($body['tags'])) {
            $version->updateTags($body['tags']);
        }

        if (isset($body['attributes'])) {
            $category = $this->app->make(FileCategory::class);
            $attributeValueMapFactory = $this->app->make(AttributeValueMapFactory::class);
            $attributeMap = $attributeValueMapFactory->createFromRequestData($category, $body['attributes']);
            foreach ($attributeMap->getEntries() as $entry) {
                $version->setAttribute($entry->getAttributeKey(), $entry->getAttributeValue());
            }
        }

        return $this->transform($version->getFile(), new FileTransformer(), Resources::RESOURCE_FILES);
    }

    /**
     * @OA\Post(
     *     path="/ccm/api/1.0/files/{fileID}/move",
     *     tags={"files"},
     *     summary="Move a file to a new location",
     *     security={
     *         {"authorization": {"files:update"}}
     *     },
     *     @OA\Parameter(
     *         name="fileID",
     *         in="path",
     *         description="ID of file to update",
     *         required=true
     *     ),
     *     @OA\RequestBody(ref="#/components/requestBodies/MoveFile"),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/File"),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="You do not have the proper permissions to update this resource."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="File not found"
     *     ),
     * )
     */
    public function move($fID)
    {
        $file = File::getByUUIDOrID($fID);
        if (!$file) {
            return $this->error(t('File not found'), 404);
        }

        $checker = new Checker($file);
        if (!$checker->canEditFile()) {
            return $this->error(t('You do not have access to edit this file.', 401));
        }

        $folderID = $this->request->request->get('folder');
        $folder = null;
        if ($folderID) {
            $folder = FileFolder::getByID($folderID);
        }
        if (!$folder instanceof FileFolder) {
            return $this->error(t('Unable to find specified folder'), 400);
        }

        $fp = new Checker($folder);
        if (!$fp->canAddFileType($file->getExtension())) {
            return $this->error(t('You do not have access to move this file to the specified location.'), 403);
        }

        $file->getFileNodeObject()->move($folder);

        return $this->transform($file, new FileTransformer(), Resources::RESOURCE_FILES);
    }
}
