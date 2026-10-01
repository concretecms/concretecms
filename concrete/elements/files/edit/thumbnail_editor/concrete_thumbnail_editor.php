<?php

defined('C5_EXECUTE') or die('Access Denied.');

$lang = [
    'save' => t('Save'),
];
$width = $thumbnail->getWidth();
$height = $thumbnail->getHeight();
$sizingMode = $thumbnail->getSizingMode();
$accessToken = app('token')->generate('update_thumbnail');
$uploadUrl = URL::to('/ccm/system/dialogs/file/thumbnails/edit/submit') . '?fID=' . $fileVersion->getFileID() . '&thumbnail=' . $thumbnail->getHandle();
$src = $fileVersion->getURL();
$fID = $fileVersion->getFileID();
$fvID = $fileVersion->getFileVersionID();
$thumbnailHandle = $thumbnail->getHandle();
?>

<div data-vue="cms">
    <concrete-thumbnail-editor
        upload-url="<?=$uploadUrl?>"
        access-token="<?=$accessToken?>"
        :width="<?=$width?>"
        :height="<?=$height?>"
        sizing-mode="<?=$sizingMode?>"
        :file-id="<?=$fID?>"
        :file-version-id="<?=$fvID?>"
        thumbnail-handle="<?=$thumbnailHandle?>"
        :lang='<?=json_encode($lang)?>'
        src="<?=$src?>"
    ></concrete-thumbnail-editor>
</div>