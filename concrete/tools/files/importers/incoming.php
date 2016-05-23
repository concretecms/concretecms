<?php

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
    $frp = new Permissions($fr);
    if (!$frp->canEditFileContents()) {
        $error->add(t('You do not have permission to modify this file.'));
    }
} else {
    $fr = false;
}

$searchInstance = $_POST['searchInstance'];
$r = new FileEditResponse();

$files = array();
if ($valt->validate('import_incoming') && !$error->has()) {
    if (!empty($_POST)) {
        $fi = new FileImporter();
        foreach ($_POST as $k => $name) {
            if (preg_match("#^send_file#", $k)) {
                if (!$fp->canAddFileType($cf->getExtension($name))) {
                    $resp = FileImporter::E_FILE_INVALID_EXTENSION;
                } else {
                    $folder = null;
                    if (isset($_POST['currentFolder'])) {
                        $node = \Concrete\Core\Tree\Node\Node::getByID($_POST['currentFolder']);
                        if ($node instanceof \Concrete\Core\Tree\Node\Type\FileFolder) {
                            $folder = $node;
                        }
                    }

                    if (!$fr && $folder) {
                        $fr = $folder;
                    }

                    $resp = $fi->importIncomingFile($name, $fr);
                }
                if (!($resp instanceof \Concrete\Core\Entity\File\Version)) {
                    $error->add($name . ': ' . FileImporter::getErrorMessage($resp));
                } else {
                    $files[] = $resp;
                    if ($_POST['removeFilesAfterPost'] == 1) {
                        $fsl = \Concrete\Core\File\StorageLocation\StorageLocation::getDefault()->getFileSystemObject();
                        $fsl->delete(REL_DIR_FILES_INCOMING . '/' . $name);
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
$r->setFiles($files);
$r->setMessage(t2('%s file imported successfully.', '%s files imported successfully', count($files)));
$r->outputJSON();
