<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var $theme \Concrete\Core\Page\Theme\Theme
 * @var $customizer \Concrete\Core\StyleCustomizer\Customizer\Customizer
 */
$presets = $customizer->getPresets();
$customSkins = [];
if ($customizer->supportsCustomSkins()) {
    $customSkins = $theme->getCustomSkins();
}

?>

<section>
    <header><h5><?= t('Presets') ?></h5></header>
    <menu>
        <?php
        foreach ($presets as $preset) { ?>
            <li>
                <a href="#" <?php if ($activeSkin == $preset->getIdentifier()) { ?>class="ccm-panel-menu-parent-item-active"<?php } ?> data-launch-sub-panel-url="<?= URL::to('/ccm/system/panels/theme/customize/preset', $theme->getThemeID(), $preset->getIdentifier(), $previewPage->getCollectionID()) ?>"
                   <?php if ($previewPage) {?>
                        data-panel-detail-url="<?=URL::to('/ccm/system/panels/details/theme/preview_preset', $theme->getThemeID(), $preset->getIdentifier(), $previewPage->getCollectionID())?>"
                   <?php } ?>
                   data-launch-panel-detail="customize-preset-<?=$preset->getIdentifier()?>"
                   data-panel-transition="fade">
                    <?=$preset->getName()?>
                </a>
            </li>
        <?php } ?>
    </menu>
    <?php if (count($customSkins)) { ?>
        <header><h5><?= t('Custom Skins') ?></h5></header>
        <menu>
            <?php
            foreach ($customSkins as $skin) { ?>
                <li>
                    <a href="#" <?php if ($activeSkin == $skin->getIdentifier()) { ?>class="ccm-panel-menu-parent-item-active"<?php } ?> data-launch-sub-panel-url="<?= URL::to('/ccm/system/panels/theme/customize/skin', $theme->getThemeID(), $skin->getIdentifier(), $previewPage->getCollectionID()) ?>"
                        <?php if ($previewPage) {?>
                            data-panel-detail-url="<?=URL::to('/ccm/system/panels/details/theme/preview_skin', $theme->getThemeID(), $skin->getIdentifier(), $previewPage->getCollectionID())?>"
                        <?php } ?>
                       data-launch-panel-detail="customize-skin-<?=$skin->getIdentifier()?>"
                       data-panel-transition="fade">
                        <?=$skin->getName()?>
                    </a>
                </li>
            <?php } ?>
        </menu>
    <?php } ?>
</section>

<script src="//ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js"></script>

