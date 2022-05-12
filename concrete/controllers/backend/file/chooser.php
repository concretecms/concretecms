<?php

namespace Concrete\Controller\Backend\File;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Entity\File\ExternalFileProvider\ExternalFileProvider;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Entity\Search\SavedFileSearch;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\File\ExternalFileProvider\ExternalFileList;
use Concrete\Core\File\ExternalFileProvider\ExternalFileProviderFactory;
use Concrete\Core\File\ExternalFileProvider\ExternalSearchRequest;
use Concrete\Core\File\ExternalFileProvider\Type\Type;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\FileTransformer;
use Concrete\Core\File\FolderItemList;
use Concrete\Core\File\Set\Set as FileSet;
use Concrete\Core\Navigation\Breadcrumb\FileManagerBreadcrumbFactory;
use Concrete\Core\Navigation\NavigationItemTransformer;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\Pagination\PaginationFactory;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Tree\Node\NodeTransformer;
use Doctrine\ORM\EntityManager;
use League\Fractal\Manager;
use League\Fractal\Pagination\PagerfantaPaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;
use Symfony\Component\HttpFoundation\JsonResponse;

class Chooser extends Controller
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Manager
     */
    protected $manager;

    public function __construct(Filesystem $filesystem, Manager $manager)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
        $this->manager = $manager->setSerializer(new DataArraySerializer());
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\AbstractController::shouldRunControllerTask()
     */
    public function shouldRunControllerTask()
    {
        $folder = $this->filesystem->getRootFolder();
        $fp = new Checker($folder);

        return $fp->canSearchFiles();
    }

    public function getRecent()
    {
        $list = new FileList();
        $list->sortByDateAddedDescending();
        $list->setItemsPerPage(20);
        $list->setPermissionsChecker(function ($file) {
            $fp = new Checker($file);
            return $fp->canViewFileInFileManager();
        });
        return $this->buildFileListFractalResponse($list);
    }

    public function getFileSets()
    {
        $mySets = [];
        foreach (FileSet::getMySets() as $set) {
            $mySets[] = ['id' => $set->fsID, 'name' => $set->fsName];
        }

        return new JsonResponse($mySets);
    }

    public function getFileSetFiles($id)
    {
        if ($this->app->make('helper/validation/numbers')->integer($id)) {
            $set = FileSet::getByID($id);
            if (is_object($set)) {
                $list = new FileList();
                $list->filterBySet($set);
                $list->setPermissionsChecker(function ($file) {
                    $fp = new Checker($file);
                    return $fp->canViewFileInFileManager();
                });
                $list->sortBy('f.fDateAdded', 'desc');
                $list->setItemsPerPage(20);

                return $this->buildFileListFractalResponse($list);
            }
        }

        throw new \Exception(t('Access Denied'));
    }

    public function getSearchPresets()
    {
        $em = $this->app->make(EntityManager::class);
        $searchRepo = $em->getRepository(SavedFileSearch::class);
        $presets = $searchRepo->findAll();

        $searchPresets = [];
        foreach ($presets as $preset) {
            $searchPresets[] = ['id' => $preset->getId(), 'presetName' => $preset->getPresetName()];
        }

        return new JsonResponse($searchPresets);
    }

    public function getSearchPresetFiles($id)
    {
        $em = $this->app->make(EntityManager::class);
        $preset = $em->find(SavedFileSearch::class, $id);
        if (is_object($preset)) {
            $query = $this->app->make(QueryFactory::class)->createFromSavedSearch($preset);
            $list = new FolderItemList();
            $list->enableAutomaticSorting();
            foreach ($query->getFields() as $field) {
                $field->filterList($list);
            }

            $columns = $query->getColumns();
            if (is_object($columns)) {
                $column = $columns->getDefaultSortColumn();
                $list->sortBySearchColumn($column);
            }

            $list->getQueryObject()->andWhere('nt.treeNodeTypeHandle = \'file\'');
            $list->setItemsPerPage($query->getItemsPerPage());

            return $this->buildFileListFractalResponse($list);
        }
    }

    public function getFolderFiles($folderId = null)
    {
        $numberValidator = $this->app->make('helper/validation/numbers');
        $session = $this->app->make('session');
        if ($numberValidator->integer($folderId)) {
            $folder = $this->filesystem->getFolder($folderId);
            if (!is_object($folder)) {
                $error = $this->app->make('error');
                $error->add(t('Unable to find the specified folder.'));

                return new JsonResponse($error);
            }
        } else {
            $folder = $this->filesystem->getRootFolder();
        }

        $permissions = new Checker($folder);
        if ($permissions->canSearchFiles()) {
            $session->set('concrete.file_manager.chooser.folder_id', $folder->getTreeNodeID());
            $list = new FolderItemList();
            $list->filterByParentFolder($folder);
            $list->getQueryObject()->addOrderBy('fv.fvType');
            $list->sortBy('dateModified', 'desc');
            $list->setItemsPerPage(20);
            $list->enableAutomaticSorting();

            return $this->buildFileListFractalResponse($list);
        }

        throw new \Exception(t('Access Denied'));
    }

    public function searchFiles($keyword)
    {
        $list = new FileList();
        $list->setPermissionsChecker(function ($file) {
            $fp = new Checker($file);
            return $fp->canViewFileInFileManager();
        });
        $list->filterByKeywords($keyword);
        $list->sortBy('f.fDateAdded', 'desc');
        $list->setItemsPerPage(20);

        return $this->buildFileListFractalResponse($list);
    }

    public function getBreadcrumb($folderId = null)
    {
        if ($this->app->make('helper/validation/numbers')->integer($folderId)) {
            $folder = $this->filesystem->getFolder($folderId);
            if (!is_object($folder)) {
                $error = $this->app->make('error');
                $error->add(t('Unable to find the specified folder.'));

                return new JsonResponse($error);
            }
        } else {
            $folder = $this->filesystem->getRootFolder();
        }

        $permissions = new Checker($folder);
        if ($permissions->canSearchFiles()) {
            $breadcrumbFactory = $this->app->make(FileManagerBreadcrumbFactory::class);
            $breadcrumb = $breadcrumbFactory->getBreadcrumb($folder);

            $collection = new Collection($breadcrumb, new NavigationItemTransformer());
            $response = $this->manager->createData($collection);

            return JsonResponse::fromJsonString($response->toJson());
        }

        throw new \Exception(t('Access Denied'));
    }

    /**
     * @param FileList|FolderItemList $list
     *
     * @return JsonResponse
     */
    protected function buildFileListFractalResponse($list): JsonResponse
    {
        $adapter = $list->getPaginationAdapter();
        $paginationFactory = $this->app->make(PaginationFactory::class, ['request' => $this->request]);
        $pagination = $paginationFactory->deliverPaginationObject($list, new Pagination($list, $adapter));

        $transformer = ($list instanceof FileList) ? new FileTransformer() : new NodeTransformer();
        $collection = new Collection($pagination->getCurrentPageResults(), $transformer);
        $collection->setPaginator(new PagerfantaPaginatorAdapter($pagination, function ($page) {
            return $page;
        }));

        $collection->setMeta([
            'query_params' => [
                'pagination_page' => $list->getQueryPaginationPageParameter(),
                'sort_column' => $list->getQuerySortColumnParameter(),
                'sort_direction' => $list->getQuerySortDirectionParameter(),
            ],
        ]);

        $response = $this->manager->createData($collection);

        return JsonResponse::fromJsonString($response->toJson());
    }

    public function importExternal($externalFileProviderId, $fileId)
    {
        /** @var ExternalFileProviderFactory $externalFileProviderFactory */
        $externalFileProviderFactory = $this->app->make(ExternalFileProviderFactory::class);
        $externalFileProvider = $externalFileProviderFactory->fetchByID($externalFileProviderId);
        /** @var ErrorList $error */
        $error = $this->app->make(ErrorList::class);

        if (!$externalFileProvider instanceof ExternalFileProvider) {
            $error->add(t('The given file provider id is invalid.'));
            return new JsonResponse($error);
        }

        $fileSystem = new Filesystem();
        $uploadDirectoryId = $fileSystem->getRootFolder()->getTreeNodeID();

        if ($this->request->request->has("uploadDirectoryId")) {
            $uploadDirectoryId = $this->request->request->get("uploadDirectoryId");
        }

        $fileVersion = $externalFileProvider->getConfigurationObject()->importFile($fileId, $uploadDirectoryId);

        if ($fileVersion instanceof Version) {
            return new JsonResponse([
                "importedFileId" => $fileVersion->getFileId()
            ]);
        } else {
            $error->add(t('There was an error while importing the file.'));
            return new JsonResponse($error);
        }
    }

    public function getExternalFileTypes($externalFileProviderId)
    {

        /** @var ExternalFileProviderFactory $externalFileProviderFactory */
        $externalFileProviderFactory = $this->app->make(ExternalFileProviderFactory::class);
        $externalFileProvider = $externalFileProviderFactory->fetchByID($externalFileProviderId);

        if (!$externalFileProvider instanceof ExternalFileProvider) {
            /** @var ErrorList $error */
            $error = $this->app->make(ErrorList::class);
            $error->add(t('The given file provider id is invalid.'));

            return new JsonResponse($error);
        }

        $config = $externalFileProvider->getConfigurationObject();

        return new JsonResponse($config->getFileTypes());
    }

    public function searchExternal($externalFileProviderId, $keyword)
    {
        /** @var ExternalFileProviderFactory $externalFileProviderFactory */
        $externalFileProviderFactory = $this->app->make(ExternalFileProviderFactory::class);
        $externalFileProvider = $externalFileProviderFactory->fetchByID($externalFileProviderId);

        if (!$externalFileProvider instanceof ExternalFileProvider) {
            /** @var ErrorList $error */
            $error = $this->app->make(ErrorList::class);
            $error->add(t('The given file provider id is invalid.'));

            return new JsonResponse($error);
        }

        $config = $externalFileProvider->getConfigurationObject();

        $selectedFileType = null;

        if ($config->supportFileTypes()) {
            $selectedFileType = $this->request->query->get("selectedFileType");
        }

        $searchRequest = new ExternalSearchRequest();

        $currentPage = (int)$this->request->query->get("ccm_paging_fl", 0);
        $itemsPerPage = (int)$this->request->query->get("itemsPerPage", 20);
        $orderBy = $this->request->query->get("ccm_order_by");
        $orderByDirection = $this->request->query->get("ccm_order_by_direction", "ASC");

        $searchRequest->setSearchTerm($keyword);
        $searchRequest->setFileType($selectedFileType);
        $searchRequest->setCurrentPage($currentPage);
        $searchRequest->setItemsPerPage($itemsPerPage);
        $searchRequest->setOrderBy($orderBy);
        $searchRequest->setOrderByDirection($orderByDirection);

        $fileList = $config->searchFiles($searchRequest);

        $totalPages = (int)$fileList->getTotalFiles() / $itemsPerPage;

        return new JsonResponse([
            "data" => $fileList,
            "meta" => [
                'query_params' => [
                    'pagination_page' => 'ccm_paging_fl',
                    'sort_column' => 'ccm_order_by',
                    'sort_direction' => 'ccm_order_by_direction'
                ],
                'pagination' => [
                    'total' => $fileList->getTotalFiles(),
                    'count' => $fileList->getTotalFiles(),
                    'per_page' => $itemsPerPage,
                    'current_page' => $currentPage,
                    'total_pages' => $totalPages,
                    'links' => []
                ]
            ]
        ]);
    }
}
