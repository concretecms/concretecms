<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php Loader::element('files/move_to_folder', array(
    'displayFolder' => function ($folder) use ($files) {
        $fp = \FilePermissions::getGlobal();
        if (!$fp->canAddFiles()) {
            return false;
        }
        foreach ($files as $f) {
            if (!$fp->canAddFileType(strtolower($f->getExtension()))) {
                return false;
            } else {
                $fileFolderObject = $f->getFileFolderObject();
                if (is_object($fileFolderObject) && $fileFolderObject->getTreeNodeID() === $folder->getTreeNodeID()) {
                    return false;
                }
            }
        }
        return true;
    },
    'getRadioButton' => function ($folder, $checked = false) use ($f) {
        $radio = id(new HtmlObject\Input('radio', 'folderID', $folder->getTreeNodeID(), array('checked' => $checked)));
                
        return $radio;
    },
));
?>
