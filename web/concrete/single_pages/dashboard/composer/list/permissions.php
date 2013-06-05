<? defined('C5_EXECUTE') or die("Access Denied."); ?>


	<? ob_start(); ?>
	<?=Loader::element('permission/help');?>
	<? $help = ob_get_contents(); ?>
	<? ob_end_clean(); ?>
	
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Composer Permissions'), $help, 'span8 offset2', false)?>
	<form method="post" action="<?=$this->action('save')?>">
	<?=Loader::helper('validation/token')->output('save_permissions')?>
	<input type="hidden" name="cmpID" value="<?=$composer->getComposerID()?>" />
	<div class="ccm-pane-body">
	<?
	$tp = new TaskPermission();
	if ($tp->canAccessComposerPermissions()) { ?>	
		<? Loader::element('permission/lists/composer', array(
			'composer' => $composer
		))?>
	<? } else { ?>
		<p><?=t('You cannot access composer permissions.')?></p>
	<? } ?>
	</div>
	<div class="ccm-pane-footer">
		<a href="<?=$this->url('/dashboard/composer/list')?>" class="btn"><?=t('Back')?></a>
		<button type="submit" value="<?=t('Save')?>" class="btn btn-primary pull-right"><?=t('Save')?> <i class="icon-ok-sign icon-white"></i></button>
	</div>
	</form>
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>