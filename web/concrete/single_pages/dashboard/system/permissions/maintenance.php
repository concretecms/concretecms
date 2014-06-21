<? defined('C5_EXECUTE') or die("Access Denied.");
$h = Loader::helper('concrete/dashboard');
$ih = Loader::helper('concrete/ui');
$form = Loader::helper('form');
echo $h->getDashboardPaneHeaderWrapper(t('Maintenance Mode'), false, 'span6 offset3', false);?>

<form id="maintenance-mode-form" action="<?php echo $view->action('')?>" method="post">
	<div class="ccm-pane-body">
		<fieldset>
			<legend style="margin-bottom: 0px"><?=t('Maintenance Mode')?></legend>
			<div class="control-group">
				<div class="controls">
					<label class="radio"><?=$form->radio('site_maintenance_mode', '1', $site_maintenance_mode)?> <span><?=t('Enabled')?></span></label>
					<label class="radio"><?=$form->radio('site_maintenance_mode', '0', $site_maintenance_mode)?> <span><?=t('Disabled')?></span></label>
				</div>
			</div>
		</fieldset>	
	</div>
	
	<div class="ccm-dashboard-form-actions-wrapper">
		<div class="ccm-dashboard-form-actions">
			<?php
				$submit = $ih->submit( t('Save'), 'maintenance-mode-form', 'right', 'primary');
				print $submit;
			?>
		</div>
	</div>
</form>
<?php echo $h->getDashboardPaneFooterWrapper(false);?>