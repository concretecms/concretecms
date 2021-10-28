<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Widget\FileFolderSelector;
use Concrete\Core\File\Filesystem;

$fileSystem = new Filesystem();

$rootFolder = $fileSystem->getRootFolder();
$folders = $rootFolder->getHierarchicalNodesOfType('file_folder', 1, true, true);
?>

<div class="form-group" id="ccm-folder-list">
    <?php
        $selector = new FileFolderSelector();
        echo $selector->selectFileFolder('folderID', isset($folderID) ? $folderID : null);
    ?>
</div>