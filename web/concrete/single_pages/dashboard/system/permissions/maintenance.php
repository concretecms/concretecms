<? defined('C5_EXECUTE') or die("Access Denied.");
$h = Loader::helper('concrete/dashboard');
$form = Loader::helper('form');
echo $h->getDashboardPaneHeaderWrapper(t('Maintenance Mode'), false, 'span6 offset3', false);?>

<form id="maintenance-mode-form" action="<?php echo $view->action('')?>" method="post" role="form">
	<?php echo $this->controller->token->output('update_maintenance')?>
	<fieldset>
		<legend style="margin-bottom: 0px"><?=t('Maintenance Mode')?></legend>
		<div class="form-group">
			<label class="radio"><?=$form->radio('site_maintenance_mode', '1', $site_maintenance_mode)?> <span><?=t('Enabled')?></span></label>
		    <label class="radio"><?=$form->radio('site_maintenance_mode', '0', $site_maintenance_mode)?> <span><?=t('Disabled')?></span></label>
		</div>
	</fieldset>	
	
	<div class="ccm-dashboard-form-actions-wrapper">
		<div class="ccm-dashboard-form-actions">    
		    <button class="pull-right btn btn-success" type="submit" ><?=t('Save')?></button>
		</div>
	</div>
</form>
<?php echo $h->getDashboardPaneFooterWrapper(false);?>