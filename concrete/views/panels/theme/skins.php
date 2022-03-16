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

$type = $customizer->getType();
$customStyle = null;
if ($type instanceof \Concrete\Core\StyleCustomizer\Customizer\Type\LegacyCustomizerType) {
    $legacyCustomizerPresetStartingPoint = null;
    // The legacy customizer type allows the ability to save customizations against a page, but has no skin-level
    // saving. This functionality is not supported going forward, it's only on the legacy customizer. If you want this
    // in the future, just make a custom skin and apply it to the page. Way more performant and easier to deal with
    // and stay organized.
    if (isset($previewPage)) {
        $manager = $type->getCustomizationsManager();
        if ($manager instanceof \Concrete\Core\StyleCustomizer\Customizations\LegacyCustomizationsManager) {
            $customStyle = $manager->getCustomStyleObjectForPage($previewPage, $theme);
            if ($customStyle) {
                foreach ($presets as $preset) {
                    if ($preset->getIdentifier() == $customStyle->getPresetHandle()) {
                        $legacyCustomizerPresetStartingPoint = $preset;
                    }
                }
            }
        }
    }
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

    <?php if ($customStyle instanceof \Concrete\Core\Page\CustomStyle) {
        // This is a page and theme using the legacy customizer, so we give an option to load the customizations
        // into the customizer. This will never happen alongside the custom skins above.
        ?>
        <header><h5><?= t('Customizations') ?></h5></header>
        <menu>
            <li>
                <a href="#" class="ccm-panel-menu-parent-item-active" data-launch-sub-panel-url="<?= URL::to('/ccm/system/panels/theme/customize/legacy', $theme->getThemeID(), $previewPage->getCollectionID()) ?>"
                    data-panel-detail-url="<?=URL::to('/ccm/system/panels/details/theme/preview_page_legacy', $theme->getThemeID(), $previewPage->getCollectionID())?>"
                   data-launch-panel-detail="customize-legacy-customizations"
                   data-panel-transition="fade">
                    <?php if ($legacyCustomizerPresetStartingPoint) { ?>
                        <?=t('%s (Modified)', $legacyCustomizerPresetStartingPoint->getName())?>
                    <?php } else { ?>
                        <?=t('Page Customizations')?>
                    <?php } ?>
                </a>
            </li>
        </menu>

    <?php } ?>
</section>

<script src="//ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js"></script>

