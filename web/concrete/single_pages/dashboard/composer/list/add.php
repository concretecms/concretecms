<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Composer'), false, 'span8 offset2', false)?>
<form class="form-horizontal" method="post" action="<?=$this->action('submit')?>">
<div class="ccm-pane-body">
<?=Loader::element('composer/form/display');?>
</div>
<div class="ccm-pane-footer">
	<button class="pull-right btn btn-primary" type="submit"><?=t('Add')?></button>
</div>
</form>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>