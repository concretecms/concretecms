<? defined('C5_EXECUTE') or die("Access Denied.");
$h = Loader::helper('concrete/dashboard');
$ih = Loader::helper('concrete/interface');
$form = Loader::helper('form');
echo $h->getDashboardPaneHeaderWrapper(t('Advanced Permissions'), false, 'span6 offset3', false);?>
<form id="permissions-form" action="<?php echo $this->action('enable_advanced_permissions')?>" method="post">
<div class="ccm-pane-body <? if (PERMISSIONS_MODEL != 'simple') { ?> ccm-pane-body-footer <? } ?>">
	<?php echo Loader::helper('validation/token')->output('enable_advanced_permissions')?>
	<? if (PERMISSIONS_MODEL != 'simple') { ?>
		<p><?=t('Advanced permissions are turned on.')?></p>
	<? } else { ?>
		<p><?=t('Advanced permissions are turned off. Enable them below.')?></p>
		<div class="block-message alert-message warning">
		<?=t('<strong>Note:</strong> Once enabled, advanced permissions cannot be turned off.')?>
		</div>
	<? } ?>
</div>
<? if (PERMISSIONS_MODEL == 'simple') { ?>
<div class="ccm-pane-footer">
<?php
	$submit = $ih->submit( t('Enable Advanced Permissions'), 'permissions-form', 'right', 'primary');
	print $submit;
?>
</div>
<? } ?>
</form>
<?php echo $h->getDashboardPaneFooterWrapper(false);?>