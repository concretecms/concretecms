<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php

$site_menu->render();

?>

<div class="ccm-dashboard-header-buttons">
    <div class="btn-group">
        <a href="javascript:void(0)" class="btn btn-danger" data-dialog="delete-site" data-dialog-title="<?=t('Delete %s', $site->getSiteName())?>"><?=t('Delete %s', $site->getSiteName())?></a>
    </div>
</div>

<div style="display: none">

    <div class="ccm-ui" data-dialog-wrapper="delete-site">
        <form method="post" action="<?=$view->action('delete_site')?>">
            <?=Loader::helper("validation/token")->output('delete_site')?>
            <input type="hidden" name="id" value="<?=$site->getSiteID()?>">
            <p><?=t('Are you sure you want to delete this site? This cannot be undone.')?></p>
            <div class="dialog-buttons">
                <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                <button class="btn btn-danger pull-right" onclick="$('div[data-dialog-wrapper=delete-site] form').submit()"><?=t('Delete Site')?></button>
            </div>
        </form>
    </div>


</div>


<div class="form-group">
    <?=$form->label('handle', t('Handle'))?>
    <div><?=$site->getSiteHandle()?></div>
</div>
<div class="form-group">
    <?=$form->label('name', t('Name'))?>
    <div><?=$site->getSiteName()?></div>
</div>
<div class="form-group">
    <?=$form->label('canonical_url', t('Canonical URL'))?>
    <div><?=$site->getSiteCanonicalURL()?></div>
</div>
<div class="form-group">
    <?=$form->label('type', t('Site Type'))?>
    <div><?=$site->getType()->getSiteTypeName()?></div>
</div>
<div class="form-group">
    <?=$form->label('type', t('Time Zone'))?>
    <div><?=$site->getConfigRepository()->get('timezone');?></div>
</div>

<script type="text/javascript">
    $(function() {
        $('div#ccm-dashboard-page').on('click', '[data-dialog]', function() {
            var width = $(this).attr('data-dialog-width');
            if (!width) {
                width = 320;
            }
            var element = 'div[data-dialog-wrapper=' + $(this).attr('data-dialog') + ']';
            jQuery.fn.dialog.open({
                element: element,
                modal: true,
                width: width,
                title: $(this).attr('data-dialog-title'),
                height: 'auto'
            });
        });
    });
</script>