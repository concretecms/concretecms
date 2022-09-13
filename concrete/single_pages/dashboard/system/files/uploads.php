<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Punic\Misc;
use Punic\Unit;

/**
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Page\View\PageView $view
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Utility\Service\Number $numberService
 * @var Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $urlResolver
 * @var bool $chunkEnabled
 * @var int|null $chunkSize
 * @var int|null $phpMaxUploadSize
 * @var int $parallelUploads
 * @var Concrete\Core\Page\Page|null $maxImageSizePage
 */
$units = [
    1 => Unit::getName('digital/byte', 'short'),
    1024 => Unit::getName('digital/kilobyte', 'short'),
    1048576 => Unit::getName('digital/megabyte', 'short'),
    1073741824 => Unit::getName('digital/gigabyte', 'short'),
];
$chunkSizeValue = $chunkSize ?? (ceil($phpMaxUploadSize / (2 * 1048576)) * 1048576);
foreach (array_reverse(array_keys($units)) as $unit) {
    if ($chunkSizeValue % $unit === 0) {
        $chunkSizeValue = $chunkSizeValue / $unit;
        $chunkSizeUnit = $unit;
        break;
    }
}
?>
<form method="POST" action="<?= $view->action('submit') ?>" id="uploads-config" v-cloak>
    <?= $token->output('ccm-system-files-uploads') ?>

    <fieldset>
        <legend><?= t('Large Files') ?></legend>
        <?= t('When users upload large files, you can configure the website to send them in smaller chunks.') ?><br />
        <?php
        if ($phpMaxUploadSize === null) {
            echo t('The website currently supports uploading large files, but you may still prefer to send them in smaller parts.');
        } else {
            $tooltip = t(
                'This value is derived from the %1$s configuration keys of the %2$s file.',
                Misc::joinAnd(['<code>upload_max_filesize</code>', '<code>post_max_size</code>']),
                '<code>php.ini</code>'
            );
            echo t(
                'The website currently support sending files with a size up to %s: if you would like to let users upload larger files you should enable the chunked uploads.',
                sprintf(
                    '<abbr class="launch-tooltip" data-bs-html="true" title="%s">%s</abbr>',
                    h($tooltip),
                    $numberService->formatSize($phpMaxUploadSize)
                )
            );
        }
        ?>
        <div class="form-group mb-0">
            <div class="form-check">
                <?= $form->checkbox('chunkEnabled', 1, $chunkEnabled) ?>
                <?= $form->label('chunkEnabled', t('Enable chunked uploads')) ?>
            </div>
        </div>
        <div class="row" v-if="chunkEnabled">
            <div class="col-lg-6">
                <div class="input-group col-auto">
                    <span class="input-group-text"><?= t('Chunk size') ?></span>
                    <?= $form->number('chunkSizeValue', $chunkSizeValue, ['step' => 1, 'min' => 1, 'required' => 'required']) ?>
                    <?= $form->select('chunkSizeUnit', $units, $chunkSizeUnit, ['required' => 'required']) ?>
                </div>
            </div>
        </div>
    </fieldset>

    <fieldset class="mt-3">
        <legend><?= t('Parallel uploads') ?></legend>
        <?= t('You can configure how many files can be sent to the web server in parallel.') ?>
        <div class="form-group">
            <div class="col col-lg-2">
                <?= $form->label('parallelUploads', t('Parallel Uploads')) ?>
                <?= $form->number('parallelUploads', $parallelUploads, ['step' => 1, 'min' => 1, 'required' => 'required']) ?>
            </div>
        </div>
    </fieldset>

    <?php
    if ($maxImageSizePage !== null) {
        ?>
        <fieldset>
            <legend><?= t('Image Resizing') ?></legend>
            <?= t('You can configure the website to resize big images <b>before</b> sending them to the server.') ?>
            <?= t(
                'This can be configured in the %s dashboard page.',
                sprintf('<a href="%s">%s</a>', h((string) $urlResolver->resolve([$maxImageSizePage])), h(t($maxImageSizePage->getCollectionName())))
            ) ?>
        </fieldset>
        <?php
    }
    ?>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="float-end">
                <button type="submit" class="btn btn-primary"><?= t('Save') ?></button>
            </div>
        </div>
    </div>

</form>
<script>
$(document).ready(function() {

new Vue({
    el: '#uploads-config',
    data() {
        return {
            chunkEnabled: false,
        };
    },
    mounted() {
        $('#chunkEnabled')
            .on('change', () => this.chunkEnabled = $('#chunkEnabled').is(':checked'))
            .trigger('change')
        ;
    },
});

});
</script>
