<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div data-view="theme-customizer-preview">
    <theme-customizer-preview
            preview-action="<?=URL::to('/ccm/system/panels/details/theme/preview_preset_iframe', $pThemeID, $presetIdentifier, $previewPage->getCollectionID())?>?ccm_token=<?=$token->generate()?>">
    </theme-customizer-preview>
</div>


<div class="ccm-panel-detail-form-actions">
    <?php if ($customizer->supportsCustomSkins()) { ?>
        <button class="float-end btn btn-success" type="button" onclick="ConcreteEvent.publish('ThemeCustomizerCreateSkin')"><?= t('Create New') ?></button>
    <?php } else { ?>
        <button class="float-end btn btn-success" type="button" onclick="ConcreteEvent.publish('ThemeCustomizerSaveStyles')"><?= t('Save Styles') ?></button>

    <?php } ?>
</div>

<script type="text/javascript">
    $(function() {
        Concrete.Vue.activateContext('cms', function (Vue, config) {
            new Vue({
                el: 'div[data-view=theme-customizer-preview]',
                components: config.components
            })
        })
    })
</script>