<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div data-view="theme-customizer-preview">
    <theme-customizer-preview
        preview-action="<?=URL::to('/ccm/system/panels/details/theme/preview_skin_iframe', $pThemeID, $skinIdentifier, $previewPage->getCollectionID())?>?ccm_token=<?=$token->generate()?>">
    </theme-customizer-preview>
</div>

<div class="ccm-panel-detail-form-actions">
    <button  <?php if ($activeSkin == $skin->getIdentifier()) { ?>disabled="disabled"<?php } ?> class="float-start btn btn-danger" type="button" onclick="ConcreteEvent.publish('ThemeCustomizerDeleteSkin')"><?= t('Delete') ?></button>
    <button class="float-end btn btn-success" type="button" onclick="ConcreteEvent.publish('ThemeCustomizerSaveSkin')"><?= t('Save Changes') ?></button>
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
