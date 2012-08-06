<?php
defined('C5_EXECUTE') or die("Access Denied.");
$dh = Loader::helper('concrete/dashboard');

echo $dh->getDashboardPaneHeaderWrapper(t('Proxy Server Settings'), t('Configures proxy server settings for your website'), 'span6 offset3', false);
?>

<form method="post" class="form-stacked" id="proxy-form"
	action="<?php echo $this->action('update_proxy'); ?>">
	<div class="ccm-pane-body">
		<?php echo $this->controller->token->output('update_proxy'); ?>

		<div class="clearfix">
			<fieldset>
				<legend>
					<?=t('Proxy Server Settings')?>
				</legend>
				<div class="clearfix">
					<?=$form->label('http_proxy_host', t('Proxy Host'));?>
					<div class="input">
						<?=$form->text('http_proxy_host', $http_proxy_host)?>
					</div>
				</div>
				<div class="clearfix">
					<?=$form->label('http_proxy_port', t('Proxy Port'));?>
					<div class="input">
						<?=$form->text('http_proxy_port', $http_proxy_port)?>
					</div>
				</div>
				<div class="clearfix">
					<?=$form->label('http_proxy_user', t('Proxy User'));?>
					<div class="input">
						<?=$form->text('http_proxy_user', $http_proxy_user)?>
					</div>
				</div>
				<div class="clearfix">
					<?=$form->label('http_proxy_pwd', t('Proxy Password'));?>
					<div class="input">
						<?=$form->text('http_proxy_pwd', $http_proxy_pwd)?>
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<div class="ccm-pane-footer">
		<?php echo $interface->submit(t('Save'), 'proxy-form', 'right', 'primary'); ?>
	</div>
</form>

<?php echo $dh->getDashboardPaneFooterWrapper(false); ?>