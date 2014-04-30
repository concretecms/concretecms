<?
defined('C5_EXECUTE') or die("Access Denied.");
$pk = PermissionKey::getByHandle('customize_themes');
?>


<section id="ccm-panel-page-design-customize">
    <form data-form="panel-page-design-customize" target="ccm-page-preview-frame" method="post" action="<?=$controller->action("preview", $theme->getThemeID())?>">
    <header><a href="" data-panel-navigation="back" class="ccm-panel-back"><span class="glyphicon glyphicon-chevron-left"></span></a> <?=t('Customize Theme')?></header>

    <div class="ccm-panel-content-inner">

    <div class="list-group">
        <div class="list-group-item list-group-item-header"><?=t('Preset')?></div>
        <?
        foreach($presets as $preset) { ?>
            <label class="list-group-item"><input type="radio" class="ccm-flat-radio" value="<?=$preset->getPresetFilename()?>" name="ptPresetID" <? if ($preset->isDefaultPreset()) { ?>checked="checked"<? } ?> /> <?=$preset->getPresetName()?>
                <?=$preset->getPresetIconHTML()?>
            </label>
        <? } ?>
    </div>

    </div>

    <div id="ccm-panel-page-design-customize-list">
    <? foreach($styleSets as $set) { ?>
        <div class="ccm-panel-page-design-customize-style-set">
            <h5><?=$set->getName()?></h5>
            <ul class="list-unstyled">
            <? foreach($set->getStyles() as $style) { ?>
                <li><?=$style->getName()?>
                <?
                $value = $c->getCustomStyleValueObject($style);
                ?>
                <?=$style->render($value)?>
                </li>
            <? } ?>
            </ul>
        </div>

    <? } ?>
    </div>

    <div style="text-align: center">
        <br/>
       <button class="btn-danger btn" data-panel-detail-action="reset"><?=t('Reset Customizations')?></button>
        <br/><br/>
   </div>

    </form>
</section>


<script type="text/javascript">

    ConcretePageDesignPanel = {

        applyDesignToPage: function() {
            var $form = $('form[data-form=panel-page-design-customize]'),
                panel = ConcretePanelManager.getByIdentifier('page');

            $form.prop('target', null);
            $form.attr('action', '<?=$controller->action("apply_to_page", $theme->getThemeID())?>');
            $form.concreteAjaxForm();
            $form.submit();
        },

        applyDesignToSite: function() {
            var $form = $('form[data-form=panel-page-design-customize]'),
                panel = ConcretePanelManager.getByIdentifier('page');

            $form.prop('target', null);
            $form.attr('action', '<?=$controller->action("apply_to_site", $theme->getThemeID())?>');
            $form.concreteAjaxForm();
            $form.submit();
        },

        resetPageDesign: function() {
            var $form = $('form[data-form=panel-page-design-customize]'),
                panel = ConcretePanelManager.getByIdentifier('page');

            $form.prop('target', null);
            $form.attr('action', '<?=$controller->action("reset_page_customizations")?>');
            $form.concreteAjaxForm();
            $form.submit();
        },

        resetSiteDesign: function() {
            var $form = $('form[data-form=panel-page-design-customize]'),
                panel = ConcretePanelManager.getByIdentifier('page');

            $form.prop('target', null);
            $form.attr('action', '<?=$controller->action("reset_site_customizations", $theme->getThemeID())?>');
            $form.concreteAjaxForm();
            $form.submit();
        }


    }

    $(function() {
        panel = ConcretePanelManager.getByIdentifier('page');
        $('button[data-panel-detail-action=submit]').unbind().on('click', function() {
            <? if ($pk->can()) { ?>
                panel.showPanelConfirmationMessage('page-design-customize-apply', "<?=t('Apply this design to just this page, or your entire site?')?>", [
                    {'class': 'btn btn-primary pull-right', 'onclick': 'ConcretePageDesignPanel.applyDesignToSite()', 'style': 'margin-left: 10px', 'text': '<?=t("Entire Site")?>'},
                    {'class': 'btn btn-default pull-right', 'onclick': 'ConcretePageDesignPanel.applyDesignToPage()', 'text': '<?=t("This Page")?>'}
                ]);
            <? } else { ?>
                ConcretePageDesignPanel.applyDesignToPage();
            <? } ?>
            return false;
        });
        $('button[data-panel-detail-action=reset]').unbind().on('click', function() {
            <? if ($pk->can()) { ?>
                panel.showPanelConfirmationMessage('page-design-customize-apply', "<?=t('Reset the theme customizations for just this page, or your entire site?')?>", [
                    {'class': 'btn btn-primary pull-right', 'onclick': 'ConcretePageDesignPanel.resetSiteDesign()', 'style': 'margin-left: 10px', 'text': '<?=t("Entire Site")?>'},
                    {'class': 'btn btn-default pull-right', 'onclick': 'ConcretePageDesignPanel.resetPageDesign()', 'text': '<?=t("This Page")?>'}
                ]);
            <? } else { ?>
                ConcretePageDesignPanel.resetPageDesign();
            <? } ?>
            return false;
        });

       // $('div.ccm-page-design-customize-font-swatch').FontPanel();
        //$('div.ccm-theme-style-custom').CustomPanel();
        $('.ccm-panel-page-design-customize-style-set input[type=hidden]').on('change', function() {
            $('form[data-form=panel-page-design-customize]').submit();
        });
    });
</script>
