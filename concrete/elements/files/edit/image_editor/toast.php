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
<style>
#ccm-tui-image-editor-buttons {
    background-color: hsla(0,0%,100%,.06);
    border-radius: 20px;
    padding: 0;
    position: absolute;
    right: 10px;
    top: 0;
    text-align: center;
    vertical-align: middle;
    list-style-type: none;
    padding: 0 10px;
}
#ccm-tui-image-editor-buttons li {
    display: inline-block;
    margin: 0;
    padding: 0;
}
#ccm-tui-image-editor-buttons a {
    color: #fff;
    font-size: 1.3rem;
    border-radius: 2px;
    display: inline-block;
    font-weight: normal;
    padding: 10px;
    opacity: 0.5;
}
#ccm-tui-image-editor-buttons a:hover {
    opacity: 1;
}
</style>

<div id="tui-image-editor-container"></div>

<div class="d-none">
    <ul id="ccm-tui-image-editor-buttons">
        <li>
            <a href="#" v-on:click.prevent="cancel" title="<?= t('Cancel') ?>">
                <i class="far fa-window-close"></i>
            </a>
        </li>
        <li>
            <a href="#" v-on:click.prevent="toggleFullScreen" title="<?= t('Full screen') ?>">
                <i v-if="fullscreen" class="fas fa-compress-arrows-alt"></i>
                <i v-else class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li>
            <a href="#" v-on:click.prevent="save" title="<?= t('Save') ?>">
                <i class="far fa-save"></i>
            </a>
        </li>
    </ul>
</div>

<script>
$(document).ready(function() {
'use strict';

const imageEditor = new window.tuiImageEditor('#tui-image-editor-container', {
    usageStatistics: false,
    includeUI: {
        loadImage: {
            path: <?= json_encode((string) $fileVersion->getURL()) ?>,
            name: <?= json_encode((string) $fileVersion->getFileName()) ?>
        },
        menuBarPosition: 'bottom',
    }
});

$('#tui-image-editor-container .tui-image-editor-header-buttons')
    .empty()
    .append($('#ccm-tui-image-editor-buttons'))
;
new Vue({
    el: '#ccm-tui-image-editor-buttons',
    data() {
        return {
            busy: false,
            fullscreen: false,
        };
    },
    mounted() {
        document.addEventListener('fullscreenchange', (e) => {
            this.fullscreen = document.fullscreenElement ? true : false;
        }, false);
    },
    methods: {
        toggleFullScreen() {
            if (this.fullscreen) {
                const showError = (e) => {
                    ConcreteAlert.error({
                        plainTextMessage: true,
                        message: <?= json_encode(t('Failed to exit from full-screen mode: %s')) ?>.replace(/%s/, e.message || e.toString()),
                    });
                };
                try {
                    document.exitFullscreen().catch((e) => showError(e));
                } catch (e) {
                    showError(e);
                }
            } else {
                const showError = (e) => {
                    ConcreteAlert.error({
                        plainTextMessage: true,
                        message: <?= json_encode(t('Failed to switch to full-screen mode: %s')) ?>.replace(/%s/, e.message || e.toString()),
                    });
                };
                try {
                    document.getElementById('tui-image-editor-container').requestFullscreen().catch((e) => showError(e));
                } catch (e) {
                    showError(e);
                }
            }
        },
        cancel() {
            if (this.busy) {
                return;
            }
            this.$destroy();
            imageEditor.destroy();
            $.fn.dialog.closeTop();
        },
        save() {
            if (this.busy) {
                return;
            }
            const url = CCM_DISPATCHER_FILENAME + '/ccm/system/file/edit/save/<?= $fileVersion->getFileID() ?>';
            const format = '<?php
                switch ($fileVersion->getType()) {
                    case 'JPEG':
                        echo 'jpeg';
                        break;
                    default:
                        echo 'png';
                        break;
                }
            ?>';
            this.busy = true;
            $.concreteAjax({
                dataType: 'json',
                data: {
                    token: CCM_SECURITY_TOKEN,
                    imageData: imageEditor.toDataURL({format: format})
                },
                type: 'POST',
                url: url,
                success: () => {
                    this.busy = false;
                    this.$destroy();
                    imageEditor.destroy();
                    $.fn.dialog.closeTop();
                    window.location.reload();
                },
                error: (xhr) => {
                    this.busy = false;
                    ConcreteAlert.dialog(ccmi18n.error, ConcreteAjaxRequest.renderErrorResponse(xhr, true));
                }
            });
        },
    },
});

});
</script>
