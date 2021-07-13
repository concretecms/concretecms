<?php
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var int $pThemeID
 * @var string $skinIdentifier
 * @var \Concrete\Core\Page\Page $previewPage
 * @var \Concrete\Core\StyleCustomizer\Skin\SkinInterface $skin
 * @var string $activeSkin
 */
?>

<div id="ccm-theme-preview-frame-wrapper">
<iframe class="ccm-page-preview-frame" name="ccm-theme-preview-frame"
    src="<?=URL::to('/ccm/system/panels/details/theme/do_preview', $pThemeID, $skinIdentifier, $previewPage->getCollectionID())?>?ccm_token=<?=$token->generate()?>">
</iframe>
</div>

<div class="ccm-panel-detail-form-actions">
    <?php if ($skin instanceof \Concrete\Core\Entity\Page\Theme\CustomSkin) { ?>

        <button  <?php if ($activeSkin == $skin->getIdentifier()) { ?>disabled="disabled"<?php } ?> class="float-left btn btn-danger" type="button" onclick="ConcreteEvent.publish('ThemeCustomizerDeleteSkin')"><?= t('Delete') ?></button>
        <button class="float-right btn btn-success" type="button" onclick="ConcreteEvent.publish('ThemeCustomizerSaveSkin')"><?= t('Save Changes') ?></button>
    <?php } else { ?>
        <button class="float-right btn btn-success" type="button" onclick="ConcreteEvent.publish('ThemeCustomizerCreateSkin')"><?= t('Create New') ?></button>
    <?php } ?>
</div>

