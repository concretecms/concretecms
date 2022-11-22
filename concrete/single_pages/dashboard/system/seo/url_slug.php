<?php

defined('C5_EXECUTE') or die('Access Denied');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Seo\UrlSlug $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var int $segmentMaxLength
 * @var bool $enableSlugAsciify
 */

?>
<form method="POST" action="<?= $controller->action('save') ?>">
    <?php $token->output('url_slug_save') ?>

    <div class="form-group">
        <?= $form->label('segment_max_length', t('Segment Max Length')) ?>
        <?= $form->number('segment_max_length', $segmentMaxLength) ?>
        <p class="form-text"><?= t('Max length of URL slug. Default is 128.') ?></p>
    </div>
    <div class="form-group">
        <div class="form-check">
            <?= $form->checkbox('enable_slug_asciify', '1', $enableSlugAsciify) ?>
            <label class="form-check-label"
                   for="enable_slug_asciify"><?= t('Enable Asciify when generating URL slug') ?></label>
            <p class="form-text"><?= t("Translate multibyte characters to ASCII characters when generating a URL slug (e.g. J'étudie le français to jetudie-le-francais).") ?><br>
            <?= t("Remove multibyte characters when this option is disabled (e.g. J'étudie le français to jtudie-le-franais).") ?></p>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="float-end">
                <button type="submit" class="btn btn-primary"><?= t('Save') ?></button>
            </div>
        </div>
    </div>
</form>
