<?php

namespace Concrete\Controller\Backend;

use Concrete\Core\Application\EditResponse;
use Concrete\Core\Command\Batch\Batch as BatchBuilder;
use Concrete\Core\Controller\Controller;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\Entity\File\Folder\FavoriteFolder;
use Concrete\Core\Entity\File\Version as FileVersionEntity;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\Command\RescanFileCommand;
use Concrete\Core\File\EditResponse as FileEditResponse;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\Importer;
use Concrete\Core\File\Incoming;
use Concrete\Core\File\Rescanner;
use Concrete\Core\File\Service\VolatileDirectory;
use Concrete\Core\File\Type\TypeList as FileTypeList;
use Concrete\Core\File\ValidationService;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Page as CorePage;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\Url\Url;
use Concrete\Core\Utility\Service\Number;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Core\Validation\CSRF\Token;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FileSet;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use IPLib\Factory as IPFactory;
use IPLib\ParseStringFlag as IPParseStringFlag;
use IPLib\Range\Type as IPRangeType;
use Permissions as ConcretePermissions;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ZipArchive;

class File extends Controller
{
    /**
     * The file to be replaced (if any).
     *
     * @var \Concrete\Core\Entity\File\File|null|false FALSE when uninitialized, NULL when none
     */
    private $fileToBeReplaced = false;

    /**
     * The destination folder where the uploaded files should be placed.
     *
     * @var \Concrete\Core\Tree\Node\Type\FileFolder|false FALSE when uninitialized
     */
    private $destinationFolder = false;

    /**
     * The original page to be used when importing files (if any).
     *
     * @var \Concrete\Core\Page\Page|null|false FALSE when uninitialized, NULL when none
     */
    private $importOriginalPage = false;

    public function star()
    {
        $fs = FileSet::createAndGetSet('Starred Files', FileSet::TYPE_STARRED);
        $files = $this->getRequestFiles();
        $r = new FileEditResponse();
        $r->setFiles($files);
        foreach ($files as $f) {
            if ($f->inFileSet($fs)) {
                $fs->removeFileFromSet($f);
                $r->setAdditionalDataAttribute('star', false);
            } else {
                $fs->addFileToSet($f);
                $r->setAdditionalDataAttribute('star', true);
            }
        }
        $r->outputJSON();
    }

    public function rescan()
    {
        $files = $this->getRequestFiles('edit_file_contents');
        $r = new FileEditResponse();
        $r->setFiles($files);
        $error = $this->app->make('error');

        try {
            $this->doRescan($files[0]);
            $r->setMessage(t('File rescanned successfully.'));
        } catch (UserMessageException $e) {
            $error->add($e->getMessage());
        } catch (Exception $e) {
            $error->add($e->getMessage());
        }
        $r->setError($error);
        $r->outputJSON();
    }

    public function rescanMultiple()
    {
        $files = $this->getRequestFiles('edit_file_contents');
        $batch = BatchBuilder::create(t('Rescan Files'), function() use ($files) {
            foreach ($files as $file) {
                yield new RescanFileCommand($file->getFileID());
            }
        });
        return $this->dispatchBatch($batch);
    }

    public function approveVersion()
    {
        $files = $this->getRequestFiles('edit_file_contents');
        $fvID = $this->request->request->get('fvID', $this->request->query->get('fvID'));
        $fvID = $this->app->make('helper/security')->sanitizeInt($fvID);
        $fv = $files[0]->getVersion($fvID);
        if ($fv === null) {
            throw new UserMessageException(t('Invalid file version.'), 400);
        }
        $fv->approve();
        $r = new FileEditResponse();
        $r->setFiles($files);
        $r->outputJSON();
    }

    public function deleteVersion()
    {
        $token = $this->app->make('token');
        if (!$token->validate('delete-version')) {
            $files = $this->getRequestFiles('edit_file_contents');
        }
        $fvID = $this->request->request->get('fvID', $this->request->query->get('fvID'));
        $fvID = $this->app->make('helper/security')->sanitizeInt($fvID);
        $fv = $files[0]->getVersion($fvID);
        if ($fv === null || $fv->isApproved()) {
            throw new UserMessageException(t('Invalid file version.', 400));
        }
        if (!$token->validate('version/delete/' . $fv->getFileID() . '/' . $fv->getFileVersionId())) {
            throw new UserMessageException($token->getErrorMessage(), 401);
        }
        $expr = Criteria::expr();
        $criteria = Criteria::create()
            ->andWhere($expr->orX(
                $expr->neq('file', $fv->getFile()),
                $expr->neq('fvID', $fv->getFileVersionID())
            ))
            ->andWhere($expr->eq('fvPrefix', $fv->getPrefix()))
            ->andWhere($expr->eq('fvFilename', $fv->getFileName()));
        $em = $this->app->make(EntityManagerInterface::class);
        $repo = $em->getRepository(FileVersionEntity::class);
        $deleteFilesAndThumbnails = $repo->matching($criteria)->isEmpty();
        $fv->delete($deleteFilesAndThumbnails);
        $r = new FileEditResponse();
        $r->setFiles($files);
        $r->outputJSON();
    }

    public function upload()
    {
        $errors = $this->app->make('error');
        $importedFileVersions = [];
        $replacingFile = $this->getFileToBeReplaced();
        try {
            if ($post_max_size = $this->app->make('helper/number')->getBytes(ini_get('post_max_size'))) {
                if ($post_max_size < $_SERVER['CONTENT_LENGTH']) {
                    throw new UserMessageException(Importer::getErrorMessage(Importer::E_FILE_EXCEEDS_POST_MAX_FILE_SIZE), 400);
                }
            }
            $token = $this->app->make('token');
            if (!$token->validate()) {
                throw new UserMessageException($token->getErrorMessage(), 401);
            }
            $receivedFiles = $this->getReceivedFiles();
            switch (count($receivedFiles)) {
                case 0:
                    break;
                case 1:
                    $importedFileVersion = $this->handleUploadedFile($receivedFiles[0]);
                    if ($importedFileVersion !== null) {
                        $importedFileVersions[] = $importedFileVersion;
                    }
                    break;
                default:
                    if ($replacingFile !== null) {
                        throw new UserMessageException(t('Only one file should be uploaded when replacing a file.'));
                    }
                    $importedFileVersions = [];
                    foreach ($receivedFiles as $receivedFile) {
                        try {
                            $importedFileVersion = $this->handleUploadedFile($receivedFile);
                            if ($importedFileVersion !== null) {
                                $importedFileVersions[] = $importedFileVersion;
                            }
                        } catch (UserMessageException $x) {
                            $errors->add($x);
                        }
                    }
                    break;
            }
        } catch (UserMessageException $e) {
            $errors->add($e);
        }

        return $this->buildImportResponse($importedFileVersions, $errors, $replacingFile !== null);
    }

    public function getFavoriteFolders()
    {
        $editResponse = new EditResponse();
        $errors = new ErrorList();
        $user = new \Concrete\Core\User\User();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->app->make(EntityManagerInterface::class);
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->app->make(ResponseFactoryInterface::class);
        $favoriteFolderRepository = $entityManager->getRepository(FavoriteFolder::class);
        $userRepository = $entityManager->getRepository(User::class);
        $userEntity = $userRepository->findOneBy(["uID" => $user->getUserID()]);

        if (!$userEntity instanceof User) {
            $errors->add(t("You are not logged in."));
        }

        if ($errors->has()) {
            $editResponse->setError($errors);
        } else {
            $favoriteFolderList = [];

            /** @var FavoriteFolder[] $favoriteFolderEntries */
            $favoriteFolderEntries = $favoriteFolderRepository->findBy(["owner" => $userEntity]);

            foreach ($favoriteFolderEntries as $favoriteFolderEntry) {
                $favoriteFolderTreeNode = Node::getByID($favoriteFolderEntry->getTreeNodeFolderId());

                if ($favoriteFolderTreeNode instanceof FileFolder) {
                    $favoriteFolderList[$favoriteFolderTreeNode->getTreeNodeID()] = $favoriteFolderTreeNode->getTreeNodeName();
                }
            }

            $editResponse->setAdditionalDataAttribute("favoriteFolders", $favoriteFolderList);
        }

        return $responseFactory->json($editResponse);
    }

    public function addFavoriteFolder($folderId)
    {
        $editResponse = new EditResponse();
        $errors = new ErrorList();
        $user = new \Concrete\Core\User\User();

        $treeNode = Node::getByID($folderId);

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->app->make(EntityManagerInterface::class);
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->app->make(ResponseFactoryInterface::class);
        $favoriteFolderRepository = $entityManager->getRepository(FavoriteFolder::class);
        $userRepository = $entityManager->getRepository(User::class);
        $userEntity = $userRepository->findOneBy(["uID" => $user->getUserID()]);
        $favoriteFolderItem = $favoriteFolderRepository->findOneBy(["owner" => $userEntity, "treeNodeFolderId" => $folderId]);

        if (!$userEntity instanceof User) {
            $errors->add(t("You are not logged in."));
        }

        if (!(is_object($treeNode) && $treeNode instanceof FileFolder)) {
            $errors->add(t("The given folder is invalid."));
        }

        if ($favoriteFolderItem instanceof FavoriteFolder) {
            $errors->add(t("The folder is already part of the favorite list."));
        }

        if ($errors->has()) {
            $editResponse->setError($errors);
        } else {
            $favoriteFolderItem = new FavoriteFolder();
            $favoriteFolderItem->setOwner($userEntity);
            $favoriteFolderItem->setTreeNodeFolderId($folderId);

            $entityManager->persist($favoriteFolderItem);;
            $entityManager->flush();

            $editResponse->setTitle(t("Folder successfully added"));
            $editResponse->setMessage(t("The folder has been successfully added to your favorite list."));
        }

        return $responseFactory->json($editResponse);
    }

    /** @noinspection DuplicatedCode */
    public function removeFavoriteFolder($folderId)
    {
        $editResponse = new EditResponse();
        $errors = new ErrorList();
        $user = new \Concrete\Core\User\User();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->app->make(EntityManagerInterface::class);
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->app->make(ResponseFactoryInterface::class);
        $favoriteFolderRepository = $entityManager->getRepository(FavoriteFolder::class);
        $userRepository = $entityManager->getRepository(User::class);
        $userEntity = $userRepository->findOneBy(["uID" => $user->getUserID()]);
        $favoriteFolderItem = $favoriteFolderRepository->findOneBy(["owner" => $userEntity, "treeNodeFolderId" => $folderId]);

        if (!$favoriteFolderItem instanceof FavoriteFolder) {
            $errors->add(t("The given folder is not part of your favorite list."));
        }

        if ($errors->has()) {
            $editResponse->setError($errors);
        } else {
            $entityManager->remove($favoriteFolderItem);;
            $entityManager->flush();

            $editResponse->setTitle(t("Folder successfully removed"));
            $editResponse->setMessage(t("The folder has been successfully removed from the your favorite list."));
        }

        return $responseFactory->json($editResponse);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function importIncoming()
    {
        $errors = $this->app->make('error');
        $importedFileVersions = [];
        try {
            $token = $this->app->make('token');
            if (!$token->validate()) {
                throw new UserMessageException($token->getErrorMessage());
            }
            $filenames = $this->request->request->get('send_file');
            if (is_string($filenames)) {
                $filenames = [$filenames];
            } elseif (!is_array($filenames)) {
                $filenames = [];
            }
            $replacingFile = $this->getFileToBeReplaced();
            switch (count($filenames)) {
                case 0:
                    throw new UserMessageException($replacingFile === null ? t('You must select at least one file.') : t('You must select one file.'));
                case 1:
                    break;
                default:
                    if ($replacingFile !== null) {
                        throw new UserMessageException(t('You must select one file.'));
                    }
                    break;
            }
            $incoming = $this->app->make(Incoming::class);
            $this->checkExistingIncomingFiles($filenames, $incoming);
            $fi = $this->app->make(Importer::class);
            $removeFilesAfterPost = (bool)$this->request->request->get('removeFilesAfterPost');
            $incomingFileSystemObject = $incoming->getIncomingFilesystem();
            $originalPage = $this->getImportOriginalPage();
            foreach ($filenames as $filename) {
                $fileVersion = $fi->importIncomingFile($filename, $replacingFile ?: $this->getDestinationFolder());
                if (!$fileVersion instanceof FileVersionEntity) {
                    $errors->add($filename . ': ' . $fi->getErrorMessage($fileVersion));
                } else {
                    if ($originalPage !== null) {
                        $fileVersion->getFile()->setOriginalPage($originalPage->getCollectionID());
                    }
                    $importedFileVersions[] = $fileVersion;
                    if ($removeFilesAfterPost) {
                        $incomingFileSystemObject->delete($incoming->getIncomingPath() . '/' . $filename);
                    }
                }
            }
        } catch (UserMessageException $x) {
            $errors->add($x);
        }

        return $this->buildImportResponse($importedFileVersions, $errors, $replacingFile !== null);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function importRemote()
    {
        $errors = $this->app->make('error');
        $importedFileVersions = [];
        try {
            $token = $this->app->make('token');
            if (!$token->validate()) {
                throw new UserMessageException($token->getErrorMessage());
            }
            $urls = $this->request->request->get('url_upload');
            if (is_string($urls)) {
                $urls = explode("\n", $urls);
            } elseif (!is_array($urls)) {
                $urls = [];
            }

            $urls = array_values(array_filter(array_map('trim', $urls), 'strlen'));
            $replacingFile = $this->getFileToBeReplaced();
            switch (count($urls)) {
                case 0:
                    throw new UserMessageException($replacingFile === null ? t('You must select at least one file.') : t('You must select one file.'));
                case 1:
                    break;
                default:
                    if ($replacingFile !== null) {
                        throw new UserMessageException(t('You must select one file.'));
                    }
                    break;
            }

            $validIps = (array) $this->checkRemoteURlsToImport($urls);

            $originalPage = $this->getImportOriginalPage();
            $fi = $this->app->make(Importer::class);
            $volatileDirectory = $this->app->make(VolatileDirectory::class);
            foreach ($urls as $url) {
                try {
                    $host = (string) \League\Url\Url::createFromUrl($url)->getHost();
                    $downloadedFile = $this->downloadRemoteURL($url, $volatileDirectory->getPath(), $validIps[$host] ?? null);
                    $fileVersion = $fi->import($downloadedFile, false, $replacingFile ?: $this->getDestinationFolder());
                    if (!$fileVersion instanceof FileVersionEntity) {
                        $errors->add($url . ': ' . $fi->getErrorMessage($fileVersion));
                    } else {
                        if ($originalPage !== null) {
                            $fileVersion->getFile()->setOriginalPage($originalPage->getCollectionID());
                        }
                        $importedFileVersions[] = $fileVersion;
                    }
                } catch (UserMessageException $x) {
                    $errors->add($x);
                }
            }
        } catch (UserMessageException $x) {
            $errors->add($x);
        }

        return $this->buildImportResponse($importedFileVersions, $errors, $replacingFile !== null);
    }

    public function duplicate()
    {
        $r = new FileEditResponse();
        /** @var Token $token */
        $token = $this->app->make(Token::class);
        /** @var \Concrete\Core\Http\Request $request */
        $request = $this->app->make(\Concrete\Core\Http\Request::class);

        if ($token->validate("", $request->request->get("token"))) {
            $files = $this->getRequestFiles('copy_file');
            $newFiles = [];
            foreach ($files as $f) {
                $nf = $f->duplicate();
                $newFiles[] = $nf;
            }
            $r->setFiles($newFiles);
        } else {
            $errorList = new ErrorList();
            $errorList->add($token->getErrorMessage());
            $r->setError($errorList);
        }
        $r->setFiles($newFiles);
        $r->outputJSON();
    }

    public function getJSON()
    {
        $files = $this->getRequestFiles();
        $r = new FileEditResponse();
        $r->setFiles($files);
        $r->outputJSON();
    }

    public function download()
    {
        $files = $this->getRequestFiles("view_file_in_file_manager", true);
        if (count($files) > 1) {
            $fh = $this->app->make('helper/file');
            $vh = $this->app->make('helper/validation/identifier');

            // zipem up
            $zipFile = $fh->getTemporaryDirectory() . '/' . $vh->getString() . '.zip';
            if (class_exists('ZipArchive', false)) {
                $zip = new ZipArchive();
                if ($zip->open($zipFile, ZipArchive::CREATE) !== true) {
                    throw new UserMessageException(t('Could not open with ZipArchive::CREATE'));
                }
                foreach ($files as $key => $f) {
                    $filename = $f->getFilename();

                    // Change the filename if it's already in the zip
                    if ($zip->locateName($filename) !== false) {
                        $extension = $fh->getExtension($filename);
                        $filename = str_replace('.' . $extension, '', $filename) . '_' . $key . '.' . $extension;
                    }
                    $zip->addFromString($filename, $f->getFileContents());
                    $f->trackDownload();
                }
                $zip->close();
                $fh->forceDownload($zipFile);
            } else {
                throw new UserMessageException('Unable to zip files using ZipArchive. Please ensure the Zip extension is installed.');
            }
        } else {
            $f = $files[0];
            $fvID = $this->request->request->get('fvID', $this->request->query->get('fvID'));
            if (!empty($fvID)) {
                $fv = $f->getVersion($fvID);
            } else {
                $fv = $f->getApprovedVersion();
            }
            $f->trackDownload();
            $f->forceDownload();
        }
    }

    /**
     * @param \Concrete\Core\Entity\File\File $f
     */
    protected function doRescan($f)
    {
        $fv = $f->getApprovedVersion();
        $rescanner = $this->app->make(Rescanner::class);
        $rescanner->rescanFileVersion($fv);
    }

    protected function getRequestFiles($permissionKey = 'view_file_in_file_manager', $checkUUID = false)
    {
        $files = [];
        $fID = $this->request->request->get('fID', $this->request->query->get('fID'));
        if (is_array($fID)) {
            $fileIDs = $fID;
        } else {
            $fileIDs = [$fID];
        }
        foreach ($fileIDs as $fID) {
            $fUUID = null;

            if (is_string($fID) && uuid_is_valid($fID)) {
                $f = \Concrete\Core\File\File::getByUUID($fID);
                $fUUID = $fID;
            } else {
                $f = \Concrete\Core\File\File::getByID($fID);
            }

            if ($f instanceof \Concrete\Core\Entity\File\File) {
                if (!$checkUUID || !$f->hasFileUUID() || $f->getFileUUID() === $fUUID) {
                    $permissionChecker = new Checker($f);
                    $responseObject = $permissionChecker->getResponseObject();

                    try {
                        if ($responseObject->validate($permissionKey)) {
                            $files[] = $f;
                        }
                    } catch (Exception $e) {
                        // Do Nothing
                    }
                }
            }
        }

        if (count($files) == 0) {
            $this->app->make('helper/ajax')->sendError(t('File not found.'));
        }

        return $files;
    }

    /**
     * @param string $property
     * @param int|null $index
     *
     * @return \Concrete\Core\Entity\File\Version|null returns NULL if the upload is chunked and we still haven't received the full file
     * @throws \Concrete\Core\Error\UserMessageException
     *
     */
    protected function handleUpload($property, $index = null)
    {
        if ($index !== null) {
            $list = $this->request->files->get($property);
            $file = is_array($list) && isset($list[$index]) ? $list[$index] : null;
        } else {
            $file = $this->request->files->get($property);
        }

        if (!$file instanceof UploadedFile) {
            throw new UserMessageException(Importer::getErrorMessage(Importer::E_FILE_INVALID));
        }
        return $this->handleUploadedFile($file);
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function handleUploadedFile(UploadedFile $file): ?FileVersionEntity
    {
        if (!$file->isValid()) {
            throw new UserMessageException(Importer::getErrorMessage($file->getError()));
        }
        $cf = $this->app->make('helper/file');

        $deleteFile = false;
        $file = $this->getFileToImport($file, $deleteFile);
        if ($file === null) {
            return null;
        }

        try {
            $name = $file->getClientOriginalName();
            $tmp_name = $file->getPathname();
            $fp = new ConcretePermissions($this->getDestinationFolder());
            if (!$fp->canAddFileType($cf->getExtension($name))) {
                throw new UserMessageException(Importer::getErrorMessage(Importer::E_FILE_INVALID_EXTENSION), 403);
            }
            $importer = $this->app->make(Importer::class);
            $importedFileVersion = $importer->import($tmp_name, $name, $this->getFileToBeReplaced() ?: $this->getDestinationFolder());
            if (!$importedFileVersion instanceof FileVersionEntity) {
                throw new UserMessageException(Importer::getErrorMessage($importedFileVersion));
            }
            $originalPage = $this->getImportOriginalPage();
            if ($originalPage !== null) {
                $importedFileVersion->getFile()->setOriginalPage($originalPage->getCollectionID());
            }
        } finally {
            if ($deleteFile) {
                @unlink($file->getPathname());
            }
        }

        return $importedFileVersion;
    }

    /**
     * Get the file instance to be replaced by the uploaded file (if any).
     *
     * @return \Concrete\Core\Entity\File\File|null
     *
     * @throws \Concrete\Core\Error\UserMessageException in case the file couldn't be found or it's not accessible
     *
     * @since 8.5.0a3
     */
    protected function getFileToBeReplaced()
    {
        if ($this->fileToBeReplaced === false) {
            $fID = $this->request->request->get('fID');
            if (!$fID) {
                $this->fileToBeReplaced = null;
            } else {
                $fID = is_scalar($fID) ? (int)$fID : 0;
                $file = $fID === 0 ? null : $this->app->make(EntityManagerInterface::class)->find(FileEntity::class, $fID);
                if ($file === null) {
                    throw new UserMessageException(t('Unable to find the specified file.'));
                }
                $fp = new Checker($file);
                if (!$fp->canEditFileContents()) {
                    throw new UserMessageException(t('You do not have permission to modify this file.'));
                }
                $this->fileToBeReplaced = $file;
            }
        }

        return $this->fileToBeReplaced;
    }

    /**
     * Get the destination folder where the uploaded files should be placed.
     *
     * @return \Concrete\Core\Tree\Node\Type\FileFolder
     *
     * @throws \Concrete\Core\Error\UserMessageException in case the folder couldn't be found or it's not accessible
     *
     * @since 8.5.0a3
     */
    protected function getDestinationFolder()
    {
        if ($this->destinationFolder === false) {
            $replacingFile = $this->getFileToBeReplaced();
            if ($replacingFile !== null) {
                $folder = $replacingFile->getFileFolderObject();
                // Fix for 5.7 files that had their parents set to their own file id
                if ($folder instanceof \Concrete\Core\Tree\Node\Type\File) {
                    $folder = $folder->getTreeNodeParentObject();
                }
            } else {
                $treeNodeID = $this->request->request->get('currentFolder');
                if ($treeNodeID) {
                    $treeNodeID = is_scalar($treeNodeID) ? (int)$treeNodeID : 0;
                    $folder = $treeNodeID === 0 ? null : Node::getByID($treeNodeID);
                } else {
                    $filesystem = new Filesystem();
                    $folder = $filesystem->getRootFolder();
                }
            }
            if (!$folder instanceof FileFolder) {
                throw new UserMessageException(t('Unable to find the specified folder.'));
            }
            if ($replacingFile === null) {
                $fp = new Checker($folder);
                if (!$fp->canAddFiles()) {
                    throw new UserMessageException(t("You don't have the permission to upload to %s", $folder->getTreeNodeDisplayName()), 400);
                }
            }
            $this->destinationFolder = $folder;
        }

        return $this->destinationFolder;
    }

    /**
     * Get the original page to be used when importing files (if any).
     *
     * @throws \Concrete\Core\Error\UserMessageException in case the file couldn't be found
     *
     * @return \Concrete\Core\Page\Page|null
     *
     * @since 8.5.0a3
     */
    protected function getImportOriginalPage()
    {
        if ($this->importOriginalPage === false) {
            $ocID = $this->request->request->get('ocID');
            if (!$ocID) {
                $this->importOriginalPage = null;
            } else {
                $ocID = is_scalar($ocID) ? (int)$ocID : 0;
                $page = $ocID === 0 ? null : CorePage::getByID($ocID);
                if ($page === null || $page->isError()) {
                    throw new UserMessageException(t('Unable to find the specified page.'));
                }
                $this->importOriginalPage = $page;
            }
        }

        return $this->importOriginalPage;
    }

    /**
     * Check that a list of strings are valid "incoming" file names.
     *
     * @param array $incomingFiles
     * @param \Concrete\Core\File\Incoming $incoming
     *
     * @throws \Concrete\Core\Error\UserMessageException in case one or more of the specified files couldn't be found
     * @throws \Exception in case of generic errors
     *
     * @since 8.5.0a3
     */
    protected function checkExistingIncomingFiles(array $incomingFiles, Incoming $incoming)
    {
        $availableFileNames = [];
        foreach ($incoming->getIncomingFilesystem()->listContents($incoming->getIncomingPath()) as $availableFile) {
            $availableFileNames[] = $availableFile['basename'];
        }
        $invalidFiles = array_diff($incomingFiles, $availableFileNames);
        switch (count($invalidFiles)) {
            case 0:
                break;
            case 1:
                throw new UserMessageException(t("The file \"%s\" can't be found in the incoming directory.", array_pop($invalidFiles)));
            default:
                throw new UserMessageException(t("These files can't be found in the incoming directory: %s", "\n- \"" . implode("\"\n- \"", $invalidFiles) . '"'));
        }
    }

    /**
     * Check that a list of strings are valid "incoming" file names.
     *
     * @param string $urls
     * @return array<string, string> An array of domains and their validated IPs
     *
     * @throws \Concrete\Core\Error\UserMessageException in case one or more of the specified URLs are not valid
     *
     * @since 8.5.0a3
     */
    protected function checkRemoteURlsToImport(array $urls)
    {
        $validIps = [];
        foreach ($urls as $u) {
            try {
                $url = Url::createFromUrl($u);
            } catch (RuntimeException $x) {
                throw new UserMessageException(t('The URL "%s" is not valid: %s', $u, $x->getMessage()));
            }
            $scheme = (string)$url->getScheme();
            if ($scheme === '') {
                throw new UserMessageException(t('The URL "%s" is not valid.', $u));
            }
            $host = trim((string)$url->getHost());
            if (in_array(strtolower($host), ['', '0', 'localhost'], true)) {
                throw new UserMessageException(t('The URL "%s" is not valid.', $u));
            }

            // If we've already validated this hostname just skip it.
            if (array_key_exists($host, $validIps)) {
                continue;
            }

            $ipFormatBlocks = [
                '/^\d+$/', // No fully integer / octal hostnames http://2130706433 http://017700000001
                '/^0x[0-9a-f]+$/i', // No Hexadecimal hostnames http://0x07f000001
            ];

            foreach ($ipFormatBlocks as $block) {
                if (preg_match($block, $host) !== 0) {
                    throw new UserMessageException(t('The URL "%s" is not valid.', $u));
                }
            }

            $ipFlags = IPParseStringFlag::IPV4_MAYBE_NON_DECIMAL | IPParseStringFlag::IPV4ADDRESS_MAYBE_NON_QUAD_DOTTED | IPParseStringFlag::MAY_INCLUDE_PORT | IPParseStringFlag::MAY_INCLUDE_ZONEID;
            $ip = IPFactory::parseAddressString($host, $ipFlags);
            if ($ip === null) {
                $dnsList = @dns_get_record($host, DNS_A | DNS_AAAA);
                while ($ip === null && $dnsList !== false && count($dnsList) > 0) {
                    $dns = array_shift($dnsList);
                    $ip = IPFactory::parseAddressString($dns['ip']);
                }
            }

            if ($ip !== null && $ip->getRangeType() !== IPRangeType::T_PUBLIC) {
                throw new UserMessageException(t('The URL "%s" is not valid.', $u));
            }

            $validIps[$host] = $ip->toString();
        }

        return $validIps;
    }

    /**
     * Download an URL to the temporary directory.
     *
     * @param string $url
     * @param string $temporaryDirectory
     *
     * @return string the local filename
     * @throws \Concrete\Core\Error\UserMessageException in case of errors
     *
     */
    protected function downloadRemoteURL($url, $temporaryDirectory, string $ip = null)
    {
        /** @var Client $client */
        $client = $this->app->make(Client::class);
        $request = new Request('GET', $url);

        $config = [
            RequestOptions::ALLOW_REDIRECTS => false,
        ];

        if ($ip) {
            $host = parse_url($url, PHP_URL_HOST);
            $scheme = parse_url($url, PHP_URL_SCHEME);
            $port = parse_url($url, PHP_URL_PORT) ?: ($scheme === 'http' ? 80 : 443);

            // Specify IP if one is provided.
            $config['curl'] = [CURLOPT_RESOLVE => ["{$host}:{$port}:{$ip}"]];
        }

        $response = $client->send($request, $config);

        if ($response->getStatusCode() !== 200) {
            throw new UserMessageException(t(/*i18n: %1$s is an URL, %2$s is an error message*/ 'There was an error downloading "%1$s": %2$s', $url, $response->getReasonPhrase() . ' (' . $response->getStatusCode() . ')'));
        }

        // figure out a filename based on filename, mimetype, ???
        $matches = null;
        if (preg_match('/^[^#\?]+[\\/]([-\w%]+\.[-\w%]+)($|\?|#)/', $request->getUri(), $matches)) {
            // got a filename (with extension)... use it
            $filename = $matches[1];
        } else {
            foreach ($response->getHeader('Content-Type') as $contentType) {
                if (!empty($contentType)) {
                    [$mimeType] = explode(';', $contentType, 2);
                    $mimeType = trim($mimeType);
                    // use mimetype from http response
                    $extension = $this->app->make('helper/mime')->mimeToExtension($mimeType);

                    if ($extension === false) {
                        throw new UserMessageException(t('Unknown mime-type: %s', h($mimeType)));
                    }
                    $filename = date('Y-m-d_H-i_') . mt_rand(100, 999) . '.' . $extension;
                } else {
                    throw new UserMessageException(t(/*i18n: %s is an URL*/ 'Could not determine the name of the file at %s', $url));
                }
            }
        }

        $fileValidationService = $this->app->make('helper/validation/file');

        if (!$fileValidationService->extension($filename)) {
            $fileHelper = $this->app->make('helper/file');
            throw new UserMessageException(t('The file extension "%s" is not valid.', $fileHelper->getExtension($filename)));
        }

        $fullFilename = $temporaryDirectory . '/' . $filename;
        // write the downloaded file to a temporary location on disk
        $handle = fopen($fullFilename, 'wb');
        fwrite($handle, $response->getBody());
        fclose($handle);

        return $fullFilename;
    }

    /**
     * @param \Concrete\Core\Entity\File\Version[] $importedFileVersions
     * @param \Concrete\Core\Error\ErrorList\ErrorList $errors
     * @param bool $isReplacingFile
     */
    protected function buildImportResponse(array $importedFileVersions, ErrorList $errors, $isReplacingFile)
    {
        $responseFactory = $this->app->make(ResponseFactoryInterface::class);
        switch ($this->request->request->get('responseFormat')) {
            case 'dropzone':
                if ($errors->has()) {
                    return $responseFactory->create(json_encode($errors->toText()), 422, ['Content-Type' => 'application/json; charset=' . APP_CHARSET]);
                }
                break;
            default:

        }
        $editResponse = new FileEditResponse();
        $editResponse->setError($errors);
        $editResponse->setFiles($importedFileVersions);
        if (count($importedFileVersions) > 0) {
            if ($isReplacingFile) {
                $editResponse->setMessage(t('File replaced successfully.'));
            } else {
                $editResponse->setMessage(t2('%s file imported successfully.', '%s files imported successfully', count($importedFileVersions)));
            }
        }

        return $responseFactory->json($editResponse);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param bool $deleteFile output parameter that's set to true if the uploaded file should be deleted manually
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile|null
     */
    private function getFileToImport(UploadedFile $file, &$deleteFile)
    {
        $deleteFile = false;
        $post = $this->request->request;
        $dzuuid = preg_replace('/[^a-z0-9\-]/i', '', $post->get('dzuuid'));
        $dzIndex = $post->get('dzchunkindex');
        $dzTotalChunks = max(0, $post->get('dztotalchunkcount'));
        if ($dzuuid && !is_null($dzIndex) && $dzTotalChunks > 0) {
            $dzIndex = (int) $dzIndex;
            $file->move($file->getPath(), $dzuuid . $dzIndex);
            if ($this->isFullChunkFilePresent($dzuuid, $file->getPath(), $dzTotalChunks)) {
                $deleteFile = true;

                return $this->combineFileChunks($dzuuid, $file->getPath(), $dzTotalChunks, $file);
            } else {
                return null;
            }
        } else {
            return $file;
        }
    }

    /**
     * @param string $fileUuid
     * @param string $tempPath
     * @param int $totalChunks
     *
     * @return bool
     */
    private function isFullChunkFilePresent($fileUuid, $tempPath, $totalChunks)
    {
        for ($i = 0; $i < $totalChunks; ++$i) {
            if (!file_exists($tempPath . DIRECTORY_SEPARATOR . $fileUuid . $i)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $fileUuid
     * @param string $tempPath
     * @param int $totalChunks
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $originalFile
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    private function combineFileChunks($fileUuid, $tempPath, $totalChunks, UploadedFile $originalFile)
    {
        $finalFilePath = $tempPath . DIRECTORY_SEPARATOR . $fileUuid;
        $finalFile = fopen($finalFilePath, 'wb');
        for ($i = 0; $i < $totalChunks; ++$i) {
            $chunkFile = $tempPath . DIRECTORY_SEPARATOR . $fileUuid . $i;
            $addition = fopen($chunkFile, 'rb');
            stream_copy_to_stream($addition, $finalFile);
            fclose($addition);
            unlink($chunkFile);
        }
        fclose($finalFile);

        return new UploadedFile($finalFilePath, $originalFile->getClientOriginalName());
    }

    public function fetchDirectories()
    {
        $directories = [];

        $editResponse = new EditResponse();
        /** @var Token $token */
        $token = $this->app->make(Token::class);
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->app->make(ResponseFactoryInterface::class);
        /** @var ErrorList $errors */
        $errors = $this->app->make(ErrorList::class);

        try {
            if ($token->validate()) {
                $filesystem = new Filesystem();
                $folder = $filesystem->getRootFolder();

                if ($folder instanceof FileFolder) {
                    $nodes = $folder->getHierarchicalNodesOfType(
                        "file_folder",
                        1,
                        true,
                        true,
                        20
                    );

                    foreach ($nodes as $node) {
                        /** @var FileFolder $treeNodeObject */
                        $treeNodeObject = $node["treeNodeObject"];
                        $nodePermissions = new Checker($treeNodeObject);
                        if ($nodePermissions->canAddFiles()) {
                            $directories[] = [
                                "directoryId" => $treeNodeObject->getTreeNodeID(),
                                "directoryName" => $treeNodeObject->getTreeNodeName(),
                                "directoryLevel" => $node["level"]
                            ];
                        }
                    }
                }
            } else {
                throw new UserMessageException($token->getErrorMessage(), 401);
            }

        } catch (UserMessageException $x) {
            $errors->add($x);
        }

        $editResponse->setError($errors);
        $editResponse->setAdditionalDataAttribute("directories", $directories);

        return $responseFactory->json($editResponse);
    }

    public function uploadComplete()
    {
        $files = $this->getRequestFiles();
        $editResponse = new EditResponse();
        $token = $this->app->make(Token::class);
        $responseFactory = $this->app->make(ResponseFactoryInterface::class);
        $errors = $this->app->make(ErrorList::class);

        if (!$files[0]) {
            $errors->add(t('Unable to retrieve file object from uploaded completion.'));
        }

        try {
            if ($token->validate()) {

                $nodes = [];
                foreach ($files as $file) {
                    $node = $file->getFileNodeObject();
                    if ($node) {
                        $nodes[] = $node->getTreeNodeID();
                    }
                }

                $this->app->make('session')->getFlashBag()->set('file_manager.updated_nodes', $nodes);

                $folder = $files[0]->getFileFolderObject();
                $redirectURL = (string)\Concrete\Core\Support\Facade\Url::to(
                    '/dashboard/files/search/', 'folder', $folder->getTreeNodeID()
                )->setQuery(['ccm_order_by' => 'dateModified', 'ccm_order_by_direction' => 'desc']);
                $editResponse->setRedirectURL($redirectURL);

            } else {
                throw new UserMessageException($token->getErrorMessage(), 401);
            }

        } catch (UserMessageException $x) {
            $errors->add($x);
        }

        $editResponse->setError($errors);
        return $responseFactory->json($editResponse);
    }

    public function createDirectory()
    {
        $directoryId = null;

        $editResponse = new EditResponse();
        /** @var Token $token */
        $token = $this->app->make(Token::class);
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->app->make(ResponseFactoryInterface::class);
        /** @var ErrorList $errors */
        $errors = $this->app->make(ErrorList::class);
        /** @var Strings $stringValidator */
        $stringValidator = $this->app->make(Strings::class);

        try {
            if ($token->validate()) {
                $directoryName = $this->request->request->get("directoryName", "");

                if (!$stringValidator->notempty($directoryName)) {
                    throw new UserMessageException(t('Folder Name cannot be empty.'), 401);
                }

                $filesystem = new Filesystem();

                $folder = null;
                if ($this->request->request->has('currentFolder')) {
                    $folder = $filesystem->getFolder($this->request->request->get('currentFolder'));
                }
                if (!$folder) {
                    $folder = $filesystem->getRootFolder();
                }

                $permissions = new Checker($folder);
                if (!$permissions->canAddTreeSubNode()) {
                    throw new UserMessageException(t('You are not allowed to create folders at this location.'), 401);
                }

                // the storage location of the root folder is used.
                $directory = $filesystem->addFolder($folder, $directoryName, $folder->getTreeNodeStorageLocationID());

                $directoryId = $directory->getTreeNodeID();
            } else {
                throw new UserMessageException($token->getErrorMessage(), 401);
            }

        } catch (UserMessageException $x) {
            $errors->add($x);
        }

        $editResponse->setError($errors);
        $editResponse->setAdditionalDataAttribute("directoryId", $directoryId);

        return $responseFactory->json($editResponse);
    }

    /** @noinspection DuplicatedCode */
    public function fetchIncomingFiles()
    {
        $files = [];
        $incomingPath = "";
        $incomingStorageLocation = "";

        $editResponse = new EditResponse();
        /** @var Token $token */
        $token = $this->app->make(Token::class);
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->app->make(ResponseFactoryInterface::class);
        /** @var ErrorList $errors */
        $errors = $this->app->make(ErrorList::class);
        /** @var ValidationService $fh */
        $fh = $this->app->make(ValidationService::class);
        /** @var Number $nh */
        $nh = $this->app->make(Number::class);
        /** @var Incoming $incoming */
        $incoming = $this->app->make(Incoming::class);

        try {
            if ($token->validate()) {
                $incomingPath = $incoming->getIncomingPath();
                $incomingStorageLocation = $incoming->getIncomingStorageLocation()->getDisplayName();

                $files = $incoming->getIncomingFilesystem()->listContents($incomingPath);

                foreach (array_keys($files) as $index) {
                    $files[$index]['allowed'] = $fh->extension($files[$index]['basename']);
                    $files[$index]['thumbnail'] = FileTypeList::getType($files[$index]['extension'])->getThumbnail();
                    $files[$index]['displaySize'] = $nh->formatSize($files[$index]['size'], 'KB');
                }
            } else {
                throw new UserMessageException($token->getErrorMessage(), 401);
            }

        } catch (UserMessageException $x) {
            $errors->add($x);
        }

        $editResponse->setError($errors);
        $editResponse->setAdditionalDataAttribute("files", $files);
        $editResponse->setAdditionalDataAttribute("incomingPath", $incomingPath);
        $editResponse->setAdditionalDataAttribute("incomingStorageLocation", $incomingStorageLocation);

        return $responseFactory->json($editResponse);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile[]
     */
    private function getReceivedFiles(): array
    {
        $receivedFiles = [];
        foreach (['file', 'files'] as $fieldName) {
            $fieldValue = $this->request->files->get($fieldName);
            if ($fieldValue instanceof UploadedFile) {
                $receivedFiles[] = $fieldValue;
            } elseif (is_array($fieldValue)) {
                foreach ($fieldValue as $fieldValueItem) {
                    if ($fieldValueItem instanceof UploadedFile) {
                        $receivedFiles[] = $fieldValueItem;
                    }
                }
            }
        }
        return $receivedFiles;
    }
}
