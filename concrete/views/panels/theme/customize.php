<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var $skin \Concrete\Core\StyleCustomizer\Skin\SkinInterface
 * @var $styles \Concrete\Core\StyleCustomizer\StyleList
 */
?>

<header>
    <a href="" data-panel-navigation="back" class="ccm-panel-back">
        <svg>
            <use xlink:href="#icon-arrow-left"/>
        </svg>
        <?= t('Customize Skin') ?>
    </a>
</header>

<section data-vue="theme-customizer">

    <theme-customizer
            <?php if (isset($skinIdentifier) && $skinIdentifier) { ?>
                preview-action="<?=URL::to('/ccm/system/panels/details/theme/preview_skin_iframe', $pThemeID, $skinIdentifier, $previewPage->getCollectionID())?>"
                delete-action="<?=URL::to('/ccm/system/panels/theme/customize/delete_skin', $pThemeID, $skinIdentifier)?>"
                save-skin-action="<?=URL::to('/ccm/system/panels/theme/customize/save_skin', $pThemeID, $skinIdentifier)?>"
            <?php } else if (isset($presetIdentifier) && $presetIdentifier) { ?>
                preview-action="<?=URL::to('/ccm/system/panels/details/theme/preview_preset_iframe', $pThemeID, $presetIdentifier, $previewPage->getCollectionID())?>"
                <?php if ($customizer->supportsCustomSkins()) { ?>
                    create-new-skin-action="<?=URL::to('/ccm/system/panels/theme/customize/create_skin', $pThemeID, $presetIdentifier)?>"
                <?php } else { ?>
                    save-styles-action="<?=URL::to('/ccm/system/panels/theme/customize/save_styles', $previewPage->getCollectionID(), $pThemeID, $presetIdentifier)?>"
                <?php } ?>
            <?php } ?>
            :styles='<?=h(json_encode($styles))?>'
            :style-list='<?=h(json_encode($styleList))?>'

            <?php if (isset($customCss)) { ?>
                :custom-css='<?=h(json_encode($customCss))?>'
            <?php } ?>
    >

    </theme-customizer>

</section>

<script type="text/javascript">
    $(function() {
        Concrete.Vue.activateContext('cms', function (Vue, config) {
            new Vue({
                el: 'section[data-vue=theme-customizer]',
                components: config.components
            })
        })
    })
</script>