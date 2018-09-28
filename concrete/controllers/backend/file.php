<?php

namespace Concrete\Controller\Backend;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\Entity\File\Version as FileVersionEntity;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\EditResponse as FileEditResponse;
use Concrete\Core\File\Importer;
use Concrete\Core\File\ImportProcessor\AutorotateImageProcessor;
use Concrete\Core\File\ImportProcessor\ConstrainImageProcessor;
use Concrete\Core\Foundation\Queue\QueueService;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\View\View;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FilePermissions;
use FileSet;
use Permissions as ConcretePermissions;
use stdClass;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class File extends Controller
{
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
        $files = $this->getRequestFiles('canEditFileContents');
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
        $files = $this->getRequestFiles('canEditFileContents');
        $q = $this->app->make(QueueService::class)->get('rescan_files');
        if ($this->request->request->get('process')) {
            $obj = new stdClass();
            $em = $this->app->make(EntityManagerInterface::class);
            $messages = $q->receive(5);
            foreach ($messages as $key => $msg) {
                // delete the page here
                $file = unserialize($msg->body);
                if ($file !== false) {
                    $f = $em->find(FileEntity::class, $file['fID']);
                    if ($f !== null) {
                        $this->doRescan($f);
                    }
                }
                $q->deleteMessage($msg);
            }
            $obj->totalItems = $q->count();
            if ($q->count() == 0) {
                $q->deleteQueue();
            }

            return $this->app->make(ResponseFactoryInterface::class)->json($obj);
        } elseif ($q->count() == 0) {
            foreach ($files as $f) {
                $q->send(serialize([
                    'fID' => $f->getFileID(),
                ]));
            }
        }

        $totalItems = $q->count();
        View::element('progress_bar', ['totalItems' => $totalItems, 'totalItemsSummary' => t2('%d file', '%d files', $totalItems)]);
    }

    public function approveVersion()
    {
        $files = $this->getRequestFiles('canEditFileContents');
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
            $files = $this->getRequestFiles('canEditFileContents');
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
            ->andWhere($expr->eq('fvFilename', $fv->getFileName()))
        ;
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
        $responseFactory = $this->app->make(ResponseFactoryInterface::class);
        try {
            $folder = null;
            if ($this->request->request->has('currentFolder')) {
                $node = Node::getByID($this->request->request->get('currentFolder'));
                if ($node instanceof FileFolder) {
                    $folder = $node;
                }
            }

            if ($folder) {
                $fp = new ConcretePermissions($folder);
            } else {
                $fp = FilePermissions::getGlobal();
            }

            if (!$fp->canAddFiles()) {
                throw new UserMessageException(t('Unable to add files.'), 400);
            }

            if ($post_max_size = $this->app->make('helper/number')->getBytes(ini_get('post_max_size'))) {
                if ($post_max_size < $_SERVER['CONTENT_LENGTH']) {
                    throw new UserMessageException(Importer::getErrorMessage(Importer::E_FILE_EXCEEDS_POST_MAX_FILE_SIZE), 400);
                }
            }

            $token = $this->app->make('token');
            if (!$token->validate()) {
                throw new UserMessageException($token->getErrorMessage(), 401);
            }

            if ($this->request->files->has('file')) {
                $files = $this->handleUpload('file', $folder);
            }
            $postedFiles = $this->request->files->get('files');
            if (is_array($postedFiles)) {
                $files = [];
                foreach (array_keys($postedFiles) as $i) {
                    $files = array_merge($files, $this->handleUpload('files', $folder, $i));
                }
            }
        } catch (UserMessageException $e) {
            return $responseFactory->error($e->getMessage());
        } catch (Exception $e) {
            $code = $e->getCode();
            if ($code) {
                return $responseFactory->error($e->getMessage(), $code);
            }
            // This error doesn't have a code, it's likely not what we're wanting.
            throw $e;
        }

        return $responseFactory->json($files);
    }

    public function duplicate()
    {
        $files = $this->getRequestFiles('canCopyFile');
        $r = new FileEditResponse();
        $newFiles = [];
        foreach ($files as $f) {
            $nf = $f->duplicate();
            $newFiles[] = $nf;
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

    /**
     * @param \Concrete\Core\Entity\File\File $f
     */
    protected function doRescan($f)
    {
        $fv = $f->getApprovedVersion();
        $resp = $fv->refreshAttributes(false);
        switch ($resp) {
            case Importer::E_FILE_INVALID:
                $errorMessage = t('File %s could not be found.', $fv->getFilename()) . '<br/>';
                throw new UserMessageException($errorMessage, 404);
        }
        $config = $this->app->make('config');
        $newFileVersion = null;
        if ($config->get('concrete.file_manager.images.use_exif_data_to_rotate_images')) {
            $processor = new AutorotateImageProcessor();
            if ($processor->shouldProcess($fv)) {
                if ($newFileVersion === null) {
                    $fv = $newFileVersion = $f->createNewVersion(true);
                }
                $processor->setRescanThumbnails(false);
                $processor->process($newFileVersion);
            }
        }
        $width = (int) $config->get('concrete.file_manager.restrict_max_width');
        $height = (int) $config->get('concrete.file_manager.restrict_max_height');
        if ($width > 0 || $height > 0) {
            $processor = new ConstrainImageProcessor($width, $height);
            if ($processor->shouldProcess($fv)) {
                if ($newFileVersion === null) {
                    $fv = $newFileVersion = $f->createNewVersion(true);
                }
                $processor->setRescanThumbnails(false);
                $processor->process($newFileVersion);
            }
        }
        $fv->rescanThumbnails();
        $fv->releaseImagineImage();
    }

    protected function getRequestFiles($permission = 'canViewFileInFileManager')
    {
        $files = [];
        $fID = $this->request->request->get('fID', $this->request->query->get('fID'));
        if (is_array($fID)) {
            $fileIDs = $fID;
        } else {
            $fileIDs = [$fID];
        }
        $em = $this->app->make(EntityManagerInterface::class);
        foreach ($fileIDs as $fID) {
            $f = $fID ? $em->find(FileEntity::class, $fID) : null;
            if ($f !== null) {
                $fp = new ConcretePermissions($f);
                if ($fp->$permission()) {
                    $files[] = $f;
                }
            }
        }

        if (count($files) == 0) {
            $this->app->make('helper/ajax')->sendError(t('File not found.'));
        }

        return $files;
    }

    protected function handleUpload($property, $folder = null, $index = false)
    {
        if ($index !== false) {
            $list = $this->request->files->get($property);
            $file = $list[$index];
        } else {
            $file = $this->request->files->get($property);
        }
        if (!$file instanceof UploadedFile) {
            throw new UserMessageException(Importer::getErrorMessage(Importer::E_FILE_INVALID));
        }
        if (!$file->isValid()) {
            throw new UserMessageException(Importer::getErrorMessage($file->getError()));
        }
        $cf = $this->app->make('helper/file');
        $name = $file->getClientOriginalName();
        $tmp_name = $file->getPathname();

        $files = [];
        if (is_object($folder)) {
            $fp = new ConcretePermissions($folder);
        } else {
            $fp = FilePermissions::getGlobal();
        }
        if (!$fp->canAddFileType($cf->getExtension($name))) {
            throw new UserMessageException(Importer::getErrorMessage(Importer::E_FILE_INVALID_EXTENSION), 403);
        } else {
            $importer = new Importer();
            $response = $importer->import($tmp_name, $name, $folder);
        }
        if (!$response instanceof FileVersionEntity) {
            throw new UserMessageException(Importer::getErrorMessage($response), 400);
        } else {
            $file = $response->getFile();
            if ($this->request->request->has('ocID')) {
                // we check $fr because we don't want to set it if we are replacing an existing file
                $file->setOriginalPage($this->request->request->get('ocID'));
            }
            $files[] = $file->getJSONObject();
        }

        return $files;
    }
}
