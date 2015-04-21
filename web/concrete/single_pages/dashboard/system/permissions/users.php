<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? ob_start(); ?>
<?=Loader::element('permission/help');?>
<? $help = ob_get_contents(); ?>
<? ob_end_clean(); ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('User Permissions'), $help, 'span8 offset2', false)?>
<form method="post" action="<?=$view->action('save')?>" role="form">
	<?=Loader::helper('validation/token')->output('save_permissions')?>
	
	<?
	$tp = new TaskPermission();
	if ($tp->canAccessTaskPermissions()) { ?>	
		<? Loader::element('permission/lists/user')?>
	<? } else { ?>
		<p><?=t('You cannot access task permissions.')?></p>
	<? } ?>

	<div class="ccm-dashboard-form-actions-wrapper">
	    <div class="ccm-dashboard-form-actions">
            <a href="<?=$view->url('/dashboard/system/permissions/users')?>" class="btn btn-default pull-left"><?=t('Cancel')?></a>
            <button class="pull-right btn btn-primary" type="submit" ><?=t('Save')?></button>
	    </div>
	</div>
</form>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>