<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Asset\Output\JavascriptFormatter;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Http\ResponseAssetGroup;

/** @var Version $fileVersion */

$group = ResponseAssetGroup::get();
$formatter = new JavascriptFormatter();
$output = $group->getAssetsToOutput();
foreach ($output as $assets) {
    foreach ($assets as $asset) {
        if ($asset instanceof Concrete\Core\Asset\Asset) {
            echo $formatter->output($asset);
        }
    }
}
?>

<div id="tui-image-editor-container"></div>

<style>
    .tui-image-editor-download-btn, .tui-image-editor-header-buttons {
        display: none !important;
    }
</style>

<div class="dialog-buttons">
    <div class="float-start">
        <button class="tui-image-editor-fullscreen-btn btn btn-secondary">
            <?= t('Full screen') ?>
        </button>
        <button class="tui-image-editor-close-btn btn btn-secondary">
            <?= t('Cancel') ?>
        </button>
    </div>
    <button class="tui-image-editor-save-btn btn btn-primary">
        <?= t('Save'); ?>
    </button>
</div>

<script>
$(document).ready(function() {
    var imageEditor = new window.tuiImageEditor('#tui-image-editor-container', {
        usageStatistics: false,
        includeUI: {
            loadImage: {
                path: <?= json_encode((string) $fileVersion->getURL()) ?>,
                name: <?= json_encode((string) $fileVersion->getFileName()) ?>
            },
            menuBarPosition: 'bottom'
        }
    });

    $('.tui-image-editor-fullscreen-btn').on('click', function () {
        document.getElementById('tui-image-editor-container').requestFullscreen();
    });

    $('.tui-image-editor-close-btn').on('click', function () {
        $.fn.dialog.closeTop();
    });

    $('.tui-image-editor-save-btn').on('click', function () {
        var url = CCM_DISPATCHER_FILENAME + '/ccm/system/file/edit/save/<?= $fileVersion->getFileID() ?>';
        <?php if ($fileVersion->getType() == 'JPEG') { ?>
            var format = 'jpeg'
        <?php } else { ?>
            var format = 'png'
        <?php } ?>

        $.concreteAjax({
            dataType: 'json',
            data: {
                token: CCM_SECURITY_TOKEN,
                imageData: imageEditor.toDataURL({format: format})
            },
            type: 'POST',
            url: url,
            success: function (r) {
                $.fn.dialog.closeTop();
                window.location.reload();
            }
        });
    });
});
</script>
