<?

defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$fp = FilePermissions::getGlobal();
use \Concrete\Core\File\EditResponse as FileEditResponse;
if (!$fp->canAddFiles()) {
	die(t("Unable to add files."));
}
$cf = Loader::helper("file");
$valt = Loader::helper('validation/token');

$error = Loader::helper('validation/error');

if (isset($_POST['fID'])) {
	// we are replacing a file
	$fr = File::getByID($_REQUEST['fID']);
} else {
	$fr = false;
}

$searchInstance = $_POST['searchInstance'];
$r = new FileEditResponse();

$files = array();
if ($valt->validate('import_incoming')) {
	if( !empty($_POST) ) {
		$fi = new FileImporter();
		foreach($_POST as $k=>$name) {
			if(preg_match("#^send_file#", $k)) {
				if (!$fp->canAddFileType($cf->getExtension($name))) {
					$resp = FileImporter::E_FILE_INVALID_EXTENSION;
				} else {
                    $fsl = Concrete\Core\File\StorageLocation\StorageLocation::getDefault()->getFileSystemObject();
                    $fre = $fsl->get(REL_DIR_FILES_INCOMING . '/' . $name);
                    if (is_object($fre)) {
                        $tmpFile = Loader::helper('file')->getTemporaryDirectory() . '/' . time() . $name;
                        file_put_contents($tmpFile, $fre->read());
                        $resp = $fi->import($tmpFile, $name, $fr);
                        $r->setMessage(t('File uploaded successfully.'));
                        if (is_object($fr)) {
                            $r->setMessage(t('File replaced successfully.'));
                        }
                    }
				}
				if (!($resp instanceof FileVersion)) {
					$error->add($name . ': ' . FileImporter::getErrorMessage($resp));
				
				} else {
					$files[] = $resp;
					if ($_POST['removeFilesAfterPost'] == 1) {
						unlink(DIR_FILES_INCOMING .'/'. $name);
					}
					
					if (!is_object($fr)) {
						// we check $fr because we don't want to set it if we are replacing an existing file
						$respf = $resp->getFile();
						$respf->setOriginalPage($_POST['ocID']);
					} else {
						$respf = $fr;
					}
				}
			}
		}
	}
	
	if (count($files) == 0) {
		$error->add(t('You must select at least one file.'));
	}

} else {
	$error->add($valt->getErrorMessage());
}

$r->setError($error);
if (is_object($respf)) {
	$r->setFile($respf);
}
$r->outputJSON();