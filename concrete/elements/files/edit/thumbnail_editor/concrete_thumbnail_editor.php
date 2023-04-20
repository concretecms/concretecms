<?php

defined('C5_EXECUTE') or die('Access Denied.');

$lang = [
    'save' => t('Save'),
];
$width = $thumbnail->getWidth();
$height = $thumbnail->getHeight();
$accessToken = app('token')->generate('update_thumbnail');
$uploadUrl = URL::to('/ccm/system/dialogs/file/thumbnails/edit/submit') . '?fID=' . $fileVersion->getFileID() . '&thumbnail=' . $thumbnail->getHandle();
$src = $fileVersion->getURL();
?>

<div data-vue="cms">
    <concrete-thumbnail-editor
        upload-url="<?=$uploadUrl?>"
        access-token="<?=$accessToken?>"
        :width="<?=$width?>"
        :height="<?=$height?>"
        :lang='<?=json_encode($lang)?>'
        src="<?=$src?>"
    ></concrete-thumbnail-editor>
</div>