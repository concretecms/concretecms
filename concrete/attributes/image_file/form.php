<?php

use Concrete\Core\Entity\Attribute\Key\Settings\ImageFileSettings;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var int $akID
 * @var Concrete\Attribute\ImageFile\Controller $controller
 * @var Concrete\Core\Entity\File\File|null $file
 * @var int $mode
 * @var array $scopeItems
 * @var Concrete\Core\Attribute\View $this
 * @var Concrete\Core\Attribute\View $view
 */

if ($mode == ImageFileSettings::TYPE_FILE_MANAGER) {

    $al = Core::make('helper/concrete/asset_library');
    echo $al->file('ccm-file-akID-' . $controller->getAttributeKey()->getAttributeKeyID(), $this->field('value'), t('Choose File'), $file);

} else {
    $htmlFileID = trim(preg_replace('/\W+/', '-', $view->field('value')), '-');
    if ($file === null) {
        ?>
        <input type="file" name="<?= h($view->field('value')) ?>" id="<?= $htmlFileID ?>" />
        <?php
    } else {
        $form = Core::make('helper/form');
        $htmlRadioReplaceID = trim(preg_replace('/\W+/', '-', $view->field('operation')), '-') . '-replace';
        $enableFileCallback = 'document.getElementById(' . json_encode($htmlFileID) . ').disabled = !document.getElementById(' . json_encode($htmlRadioReplaceID) . ').checked'
        ?>
        <input type="hidden" name="<?= $view->field('previousFile') ?>" value="<?= $file->getFileID() ?>" />
        <div class="form-check">
            <?= $form->radio($view->field('operation'), 'keep', true, ['onchange' => h($enableFileCallback)]) ?>
            <?= $form->label($view->field('operation') . '1', t('Keep existing file (%s)', h($file->getFileName()))) ?>
        </div>
        <div class="form-check">

                <?= $form->radio($view->field('operation'), 'remove', false, ['onchange' => h($enableFileCallback)]) ?>
                <?= $form->label($view->field('operation') . '2', t('Remove current file')) ?>

        </div>
        <div class="form-check">
            <?= $form->radio($view->field('operation'), 'replace', false, ['id' => $htmlRadioReplaceID, 'onchange' => h($enableFileCallback)]) ?>
            <?= $form->label($view->field('operation') . '3', t('Replace with') . ' <input type="file" name="'.h($view->field('value')).'" id="'.$htmlFileID.'" disabled="disabled" />') ?>
        </div>
        <?php
    }
    ?>
    <script>
    (function() {
        var hook = window.addEventListener ?
            function (node, eventName, callback) { node.addEventListener(eventName, callback, false); } :
            function (node, eventName, callback) { node.attachEvent('on' + eventName, callback); }
        ;

        function initialize() {
        	var fileElement = document.getElementById(<?= json_encode($htmlFileID) ?>);
            if (!fileElement) {
                return false;
            }
            for (var element = fileElement; element && element != document.body; element = element.parentNode || element.parentElement) {
                if (typeof element.nodeName === 'string' && element.nodeName.toLowerCase() === 'form') {
                    if (typeof element.enctype !== 'string' || element.enctype === '' || element.enctype.toLowerCase() === 'application/x-www-form-urlencoded') {
                        element.enctype = 'multipart/form-data';
                    }
                    break;
                }
            }
        }
        if (!initialize()) {
        	hook(window, 'load', initialize);
        }
    })();
    </script>

<?php } ?>
