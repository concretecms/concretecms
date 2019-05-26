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
        $htmlRadioIDPrefix = trim(preg_replace('/\W+/', '-', $view->field('operation')), '-') . '-';
        ?>
        <input type="hidden" name="<?= $view->field('previousFile') ?>" value="<?= $file->getFileID() ?>" />
        <div class="radio">
            <label>
                <?= $form->radio($view->field('operation'), 'keep', true, ['id' => "{$htmlRadioIDPrefix}keep"]) ?>
                <?= t('Keep existing file (%s)', h($file->getFileName())) ?>
            </label>
        </div>
        <div class="radio">
            <label>
                <?= $form->radio($view->field('operation'), 'remove', false, ['id' => "{$htmlRadioIDPrefix}remove"]) ?>
                <?= t('Remove current file') ?>
            </label>
        </div>
        <div class="radio">
            <label>
                <?= $form->radio($view->field('operation'), 'replace', false, ['id' => "{$htmlRadioIDPrefix}replace"]) ?>
                <?= t('Replace with') ?>
                <input type="file" name="<?= h($view->field('value')) ?>" id="<?= $htmlFileID ?>" disabled="disabled" />
            </label>
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

        hook(window, 'load', function () {
            var fileElement = document.getElementById(<?= json_encode($htmlFileID) ?>);
            for (var element = fileElement; element && element != document.body; element = element.parentNode || element.parentElement) {
                if (typeof element.nodeName === 'string' && element.nodeName.toLowerCase() === 'form') {
                    if (typeof element.enctype !== 'string' || element.enctype === '' || element.enctype.toLowerCase() === 'application/x-www-form-urlencoded') {
                        element.enctype = 'multipart/form-data';
                    }
                    break;
                }
            }
            <?php
            if ($file !== null) {
                ?>
                var options = {},
                    updateDisabled = function () {
                        fileElement.disabled = !options.replace.checked;
                    };
                for (var optionValues = ['keep', 'remove', 'replace'], i = 0; i < optionValues.length; i++) {
                    options[optionValues[i]] = document.getElementById(<?= json_encode($htmlRadioIDPrefix) ?> + optionValues[i]);
                    hook(options[optionValues[i]], 'change', updateDisabled);
                }
                <?php
            }
            ?>
        });
    })();
    </script>

<?php } ?>
