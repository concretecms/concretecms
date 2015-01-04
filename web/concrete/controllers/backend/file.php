<?
namespace Concrete\Controller\Backend;
use Controller;
use FileSet;
use File as ConcreteFile;
use \Concrete\Core\File\EditResponse as FileEditResponse;
use Loader;
use FileImporter;
use Exception;
use Permissions as ConcretePermissions;
use FilePermissions;
use FileVersion;

class File extends Controller {

    public function star() {
        $fs = FileSet::createAndGetSet('Starred Files', FileSet::TYPE_STARRED);
        $files = $this->getRequestFiles();
        $r = new FileEditResponse();
        $r->setFiles($files);
        foreach($files as $f) {
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

    public function rescan() {
        $files = $this->getRequestFiles('canEditFileContents');
        $r = new FileEditResponse();
        $r->setFiles($files);
        $successMessage = '';
        $errorMessage = '';
        $successCount = 0;

        foreach($files as $f) {
            try {
                $fv = $f->getApprovedVersion();
                $resp = $fv->refreshAttributes();
                switch ($resp) {
                    case \Concrete\Core\File\Importer::E_FILE_INVALID:
                        $errorMessage .= t('File %s could not be found.', $fv->getFilename()) . '<br/>';
                        break;
                    default:
                        $successCount++;
                        $successMessage = t2('%s file rescanned successfully.', '%s files rescanned successfully.',
                            $successCount);
                        break;
                }
            } catch(\Concrete\Flysystem\FileNotFoundException $e) {
                $errorMessage .= t('File %s could not be found.', $fv->getFilename()) . '<br/>';
            }
        }
        if ($errorMessage && !$successMessage) {
            $e = new \Concrete\Core\Error\Error;
            $e->add($errorMessage);
            $r->setError($e);
        } else {
            $r->setMessage($errorMessage . $successMessage);
        }
        $r->outputJSON();
    }

    public function approveVersion() {
        $files = $this->getRequestFiles('canEditFileContents');
        $r = new FileEditResponse();
        $r->setFiles($files);
        $fv = $files[0]->getVersion(\Core::make('helper/security')->sanitizeInt($_REQUEST['fvID']));
        if (is_object($fv)) {
            $fv->approve();
        } else {
            throw new Exception(t('Invalid file version.'));
        }
        $r->outputJSON();
    }

    public function deleteVersion() {
        $files = $this->getRequestFiles('canEditFileContents');
        $r = new FileEditResponse();
        $r->setFiles($files);
        $fv = $files[0]->getVersion(\Core::make('helper/security')->sanitizeInt($_REQUEST['fvID']));
        if (is_object($fv) && !$fv->isApproved()) {
            $fv->delete();
        } else {
            throw new Exception(t('Invalid file version.'));
        }
        $r->outputJSON();
    }

    protected function getRequestFiles($permission = 'canViewFileInFileManager') {
        $files = array();
        if (is_array($_REQUEST['fID'])) {
            $fileIDs = $_REQUEST['fID'];
        } else {
            $fileIDs[] = $_REQUEST['fID'];
        }
        foreach($fileIDs as $fID) {
            $f = ConcreteFile::getByID($fID);
            $fp = new ConcretePermissions($f);
            if ($fp->$permission()) {
                $files[] = $f;
            }
        }

        if (count($files) == 0) {
            throw new Exception(t("Access Denied."));
        }

        return $files;
    }

    public function upload() {
        $fp = FilePermissions::getGlobal();
        $cf = \Core::make('helper/file');
        if (!$fp->canAddFiles()) {
            throw new Exception(t("Unable to add files."));
        }
        $val = \Core::make( 'helper/validation/token' );
        if (!$val->validate()) {
            throw new Exception($val->getErrorMessage());
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

        \Core::make('helper/ajax')->sendResult($files);
    }

    public function duplicate() {
        $files = $this->getRequestFiles('canCopyFile');
        $r = new FileEditResponse();
        $newFiles = array();
        foreach($files as $f) {
            $nf = $f->duplicate();
            $newFiles[] = $nf;
        }
        $r->setFiles($newFiles);
        $r->outputJSON();
    }

    public function getJSON() {
        $files = $this->getRequestFiles();
        $r = new FileEditResponse();
        $r->setFiles($files);
        $r->outputJSON();
    }

    public function stream( $fID, $fvID ) {

        $fp = FilePermissions::getGlobal();
        $f = ConcreteFile::getByID($fID);

        if(!$f || $f->isError()) {

            if ($f && $f->getError() == \Concrete\Core\File\Importer::E_FILE_INVALID ) die(t("The requested file couldn't be found."));
            die(t("An unexpected error occurred while looking for the requested file"));
        }

        $fp = new ConcretePermissions($f);

        if ($fp->canViewFile()) {

            $fv = !is_null( $fvID ) ? $f->getVersion($_REQUEST['fvID']) : $f->getApprovedVersion();

            $f->trackDownload();
            $f->forceDownload();
        }
    }

    public function zipAndStream( array $fIDs, $fvID = null ) {

        $vh = \Core::make('helper/validation/identifier');
        $fh = \Core::make('helper/file');

        $filename = $fh->getTemporaryDirectory() . '/' . $vh->getString() . '.zip';

        $files = array();
        $filenames = array();

        foreach($fIDs as $fID) {

            $f = ConcreteFile::getByID($fID);
            if($f->isError()) continue;

            $fp = new ConcretePermissions($f);

            if ($fp->canViewFile()) {
                $files[] = $f;
                $f->trackDownload();
            }
        }

        if(empty($files)) die(t("None of the requested files could be found."));

        if(class_exists('ZipArchive', false)) {

            $zip = new \ZipArchive;
            $res = $zip->open( $filename, \ZipArchive::CREATE );

            if($res !== true) throw new Exception(t('Could not open with %s', 'ZipArchive::CREATE'));

            foreach($files as $f) $zip->addFromString($f->getFilename(), $f->getFileContents());

            $zip->close();

            $fh->forceDownload($filename);

        } else {
            throw new Exception(t('Unable to zip files using ZipArchive. Please ensure the Zip extension is installed.'));
        }

    }

    public function download() {

        $fp = FilePermissions::getGlobal();

        if (!$fp->canSearchFileSet()) die(t("Unable to search file sets."));

        $target = $_REQUEST['fID'];

        if ( !is_array($target) && isset( $_REQUEST['zipit'] ) ) $target = array( $target );
                
        if ( is_array($target) ) {
            // sanitize:
            foreach ( $target as $key => $val ) $target[$key] = intval($val);
            $this->zipAndStream($target);
            return;
        } else {
            $this->stream( intval($target), isset( $_REQUEST['fvID'] )? intval($_REQUEST['fvID']) : null );
        }
    }

}

