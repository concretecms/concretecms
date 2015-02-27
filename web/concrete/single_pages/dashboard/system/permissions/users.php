<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php ob_start(); ?>
<?=Loader::element('permission/help');?>
<?php $help = ob_get_contents(); ?>
<?php ob_end_clean(); ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('User Permissions'), $help, 'span8 offset2', false)?>
<form method="post" action="<?=$view->action('save')?>" role="form">
	<?=Loader::helper('validation/token')->output('save_permissions')?>
	
	<?php
	$tp = new TaskPermission();
	if ($tp->canAccessTaskPermissions()) { ?>	
		<?php Loader::element('permission/lists/user')?>
	<?php } else { ?>
		<p><?=t('You cannot access task permissions.')?></p>
	<?php } ?>
	
	<div class="ccm-dashboard-form-actions-wrapper">
	    <div class="ccm-dashboard-form-actions">
    		<input type="submit" value="<?=t('Save')?>" class="btn btn-success pull-right"><?=t('Save')?> <i class="icon-ok-sign icon-white"></i></button>
	    </div>
	</div>
</form>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>