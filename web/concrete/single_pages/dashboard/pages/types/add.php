<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<form method="post" action="<?=$view->action('submit')?>">
<div class="ccm-pane-body">
<?=Loader::element('page_types/form/base');?>
</div>
<div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions">
	<button class="pull-right btn btn-primary" type="submit"><?=t('Add')?></button>
</div>
</div>
</form>