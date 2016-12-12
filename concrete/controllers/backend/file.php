<?php
namespace Concrete\Controller\Backend;

use Concrete\Core\File\Importer;
use Concrete\Core\File\ImportProcessor\ConstrainImageProcessor;
use Concrete\Core\File\ImportProcessor\SetJPEGQualityProcessor;
use Concrete\Core\Foundation\Queue\Queue;
use Concrete\Core\Validation\CSRF\Token;
use Controller;
use FileSet;
use File as ConcreteFile;
use \Concrete\Core\File\EditResponse as FileEditResponse;
use Loader;
use FileImporter;
use Config;
use stdClass;
use Exception;
use Permissions as ConcretePermissions;
use FilePermissions;
use FileVersion;
use Core;

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

    protected function doRescan($f)
    {
        $resize = \Config::get('concrete.file_manager.restrict_uploaded_image_sizes');
        $processors = array();
        if ($resize) {
            $width = (int) \Config::get('concrete.file_manager.restrict_max_width');
            $height = (int) \Config::get('concrete.file_manager.restrict_max_height');
            $quality = (int) \Config::get('concrete.file_manager.restrict_resize_quality');
            $resizeProcessor = new ConstrainImageProcessor($width, $height);
            $qualityProcessor = new SetJPEGQualityProcessor($quality);
            $processors[] = $resizeProcessor;
            $processors[] = $qualityProcessor;
        }

        if (count($processors)) {
            $fv = $f->createNewVersion(true);
            foreach($processors as $processor) {
                if ($processor->shouldProcess($fv)) {
                    $processor->process($fv);
                }
            }
        } else {
            $fv = $f->getApprovedVersion();
        }
        $resp = $fv->refreshAttributes();
        switch ($resp) {
            case \Concrete\Core\File\Importer::E_FILE_INVALID:
                $errorMessage = t('File %s could not be found.', $fv->getFilename()) . '<br/>';
                throw new Exception($errorMessage);
                break;
        }
    }

    public function rescan()
    {
        $files = $this->getRequestFiles('canEditFileContents');
        $r = new FileEditResponse();
        $r->setFiles($files);
        $error = new \Concrete\Core\Error\Error;

        try {
            $this->doRescan($files[0]);
            $r->setMessage(t('File rescanned successfully.'));
        } catch (\Concrete\Flysystem\FileNotFoundException $e) {
            $errorMessage = t('File %s could not be found.', $files[0]->getFilename()) . '<br/>';
            $error->add($errorMessage);
        } catch(\Exception $e) {
            $error->add($e->getMessage());
        }
        $r->setError($error);
        $r->outputJSON();
    }

    public function rescanMultiple()
    {
        $files = $this->getRequestFiles('canEditFileContents');
        $q = Queue::get('rescan_files');
        if ($_POST['process']) {
            $obj = new stdClass();
            $messages = $q->receive(5);
            foreach($messages as $key => $msg) {
                // delete the page here
                $file = unserialize($msg->body);
                $f = \Concrete\Core\File\File::getByID($file['fID']);
                if (is_object($f)) {
                    $this->doRescan($f);
                }
                $q->deleteMessage($msg);
            }
            $obj->totalItems = $q->count();
            if ($q->count() == 0) {
                $q->deleteQueue(5);
            }
            print json_encode($obj);
            exit;
        } else if ($q->count() == 0) {
            foreach($files as $f) {
                $q->send(serialize(array(
                    'fID' => $f->getFileID()
                )));
            }
        }

        $totalItems = $q->count();
        Loader::element('progress_bar', array('totalItems' => $totalItems, 'totalItemsSummary' => t2("%d file", "%d files", $totalItems)));
        return;
    }


    public function approveVersion()
    {
        $files = $this->getRequestFiles('canEditFileContents');
        $r = new FileEditResponse();
        $r->setFiles($files);
        $fv = $files[0]->getVersion(Loader::helper('security')->sanitizeInt($_REQUEST['fvID']));
        if (is_object($fv)) {
            $fv->approve();
        } else {
            throw new Exception(t('Invalid file version.'));
        }
        $r->outputJSON();
    }

    public function deleteVersion()
    {
        /** @var Token $token */
        $token = $this->app->make('token');
        if (!$token->validate('delete-version'))

        $files = $this->getRequestFiles('canEditFileContents');
        $r = new FileEditResponse();
        $r->setFiles($files);
        $fv = $files[0]->getVersion(Loader::helper('security')->sanitizeInt($_REQUEST['fvID']));
        if (is_object($fv) && !$fv->isApproved()) {
            if (!$token->validate('version/delete/' . $fv->getFileID() . "/" . $fv->getFileVersionId())) {
                throw new Exception($token->getErrorMessage());
            }
            $fv->delete();
        } else {
            throw new Exception(t('Invalid file version.'));
        }
        $r->outputJSON();
    }

    protected function getRequestFiles($permission = 'canViewFileInFileManager')
    {
        $files = array();
        if (is_array($_REQUEST['fID'])) {
            $fileIDs = $_REQUEST['fID'];
        } else {
            $fileIDs[] = $_REQUEST['fID'];
        }
        foreach ($fileIDs as $fID) {
            $f = ConcreteFile::getByID($fID);
            $fp = new ConcretePermissions($f);
            if ($fp->$permission()) {
                $files[] = $f;
            }
        }

        if (count($files) == 0) {
            Core::make('helper/ajax')->sendError(t('File not found.'));
        }

        return $files;
    }

    public function upload()
    {
        $fp = FilePermissions::getGlobal();
        $cf = Loader::helper('file');
        if (!$fp->canAddFiles()) {
            throw new Exception(t("Unable to add files."));
        }

        if ($post_max_size = \Loader::helper('number')->getBytes(ini_get('post_max_size'))) {
            if ($post_max_size < $_SERVER['CONTENT_LENGTH']) {
                throw new Exception(FileImporter::getErrorMessage(Importer::E_FILE_EXCEEDS_POST_MAX_FILE_SIZE));
            }
        }

        if (!Loader::helper('validation/token')->validate()) {
            throw new Exception(Loader::helper('validation/token')->getErrorMessage());
        }
        $files = array();
        if (isset($_FILES['files']) && (is_uploaded_file($_FILES['files']['tmp_name'][0]))) {
            for ($i = 0; $i < count($_FILES['files']['tmp_name']); $i++) {
                if (!$fp->canAddFileType($cf->getExtension($_FILES['files']['name'][$i]))) {
                    throw new Exception(FileImporter::getErrorMessage(FileImporter::E_FILE_INVALID_EXTENSION));
                } else {
                    $importer = new FileImporter();
                    $response = $importer->import($_FILES['files']['tmp_name'][$i], $_FILES['files']['name'][$i]);
                }
                if (!($response instanceof \Concrete\Core\File\Version)) {
                    throw new Exception(FileImporter::getErrorMessage($response));
                } else {
                    $file = $response->getFile();
                    if (isset($_POST['ocID'])) {
                        // we check $fr because we don't want to set it if we are replacing an existing file
                        $file->setOriginalPage($_POST['ocID']);
                    }
                    $files[] = $file->getJSONObject();
                }
            }
        } else {
            throw new Exception(FileImporter::getErrorMessage($_FILES['Filedata']['error']));
        }

        Loader::helper('ajax')->sendResult($files);
    }

    public function duplicate()
    {
        $files = $this->getRequestFiles('canCopyFile');
        $r = new FileEditResponse();
        $newFiles = array();
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
}
