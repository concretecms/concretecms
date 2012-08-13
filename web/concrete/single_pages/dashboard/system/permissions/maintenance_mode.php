<? defined('C5_EXECUTE') or die("Access Denied.");
$h = Loader::helper('concrete/dashboard');
$ih = Loader::helper('concrete/interface');
$form = Loader::helper('form');
echo $h->getDashboardPaneHeaderWrapper(t('Maintenance Mode'), false, 'span6 offset3', false);?>
<form id="maintenance-mode-form" action="<?php echo $this->action('')?>" method="post">
<div class="ccm-pane-body">
	<?php echo $this->controller->token->output('update_maintenance')?>
	<?php if (!empty($token_error) && is_array($token_error)) { ?>
	<div class="alert-message error"><?php echo $token_error[0]?></div>
	<?php } ?>
	<div class="clearfix">
		<?php echo $form->label('site_maintenance_mode', t('Maintenance Mode'))?>
		<div class="input">
			<ul class="inputs-list">
				<li>
					<label>
						<?php echo $form->radio('site_maintenance_mode', '1', $site_maintenance_mode)?>
						<span><?php echo t('Enabled')?></span>
					</label>
				</li>
				<li>
					<label>
						<?php echo $form->radio('site_maintenance_mode', '0', $site_maintenance_mode)?>
						<span><?php echo t('Disabled')?></span>
					</label>
				</li>
			</ul>
		</div>
	</div>	
	
</div>
<div class="ccm-pane-footer">
<?php
	$submit = $ih->submit( t('Save'), 'maintenance-mode-form', 'right', 'primary');
	print $submit;
?>
</div>
</form>
<?php echo $h->getDashboardPaneFooterWrapper(false);?>