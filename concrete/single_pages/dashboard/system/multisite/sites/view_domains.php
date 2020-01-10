<?php defined('C5_EXECUTE') or die("Access Denied.");?>

<div class="ccm-dashboard-header-buttons">
    <button data-dialog="add-domain" class="btn btn-default"><?=t("Add Domain")?></button>
</div>

<?php

$site_menu->render();

?>

<div style="display: none">

    <div class="ccm-ui" data-dialog-wrapper="add-domain">
        <form method="post" action="<?=$view->action('add_domain')?>">
            <?=Loader::helper("validation/token")->output('add_domain')?>
            <input type="hidden" name="id" value="<?=$site->getSiteID()?>">
            <div class="form-group">
                <label class="control-label"><?=t('Domain')?></label>
                <input type="text" name="domain" class="form-control">
            </div>
            <div class="dialog-buttons">
                <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                <button class="btn btn-primary pull-right" onclick="$('div[data-dialog-wrapper=add-domain] form').submit()"><?=t('Add Domain')?></button>
            </div>
        </form>
    </div>


</div>

<div class="form-group">
    <label class="control-label launch-tooltip" title="<?=t('This domain is automatically constructred from the site\'s canonical URL.')?>"><?=t('Canonical Domain')?></label>
    <div><?=$canonicalDomain?></div>
</div>

<div class="form-group">
    <label class="control-label"><?=t('Additional Domains')?></label>
    <?php if (count($domains)) { ?>
    <div class="row">
        <div class="col-md-6">
        <ul class="item-select-list">
            <?php foreach($domains as $domain) { ?>
                <li><span><?=$domain->getDomain()?>
                    <a href="<?=$view->action('delete_domain', $domain->getDomainID(), $token->generate('delete_domain'))?>" class="pull-right icon-link"><i class="fa fa-close"></i></a>
               </span> </li>
            <?php } ?>
        </ul>
        </div>
    </div>
    <?php } else { ?>
        <div><?=t('None')?></div>
    <?php } ?>
</div>
