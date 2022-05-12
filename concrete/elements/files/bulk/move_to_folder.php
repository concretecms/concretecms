<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\View\View;
use HtmlObject\Input;

$folderID = null;
if (!empty($files[0])) {
    $fileFolder = $files[0]->getFileFolderObject();
    if ($fileFolder) {
        $folderID = $fileFolder->getTreeNodeID();
    }
}
/** @noinspection PhpUnhandledExceptionInspection */
View::element('files/move_to_folder', ['folderID' => $folderID]);
