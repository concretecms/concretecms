<? defined('C5_EXECUTE') or die("Access Denied.");?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Captcha Settings'), false, 'span10 offset1', (!is_object($activeCaptcha) || (!$activeCaptcha->hasOptionsForm())))?>
<form method="post" id="site-form" action="<?=$this->action('update_captcha')?>">
<? if (is_object($activeCaptcha) && $activeCaptcha->hasOptionsForm()) { ?>
	<div class="ccm-pane-body">
<? } ?>
<?=$this->controller->token->output('update_captcha')?>
	<? if (count($captchas) > 0) { ?>

		<div class="clearfix">
		<?=$form->label('activeCaptcha', t('Active Captcha'))?>
		<div class="input">
		<? 
		$activeHandle = '';
		if (is_object($activeCaptcha)) {
			$activeHandle = $activeCaptcha->getSystemCaptchaLibraryHandle();
		}
		?>
		
		<?=$form->select('activeCaptcha', $captchas, $activeHandle, array('class' => 'span4'))?>
		</div>
		</div>
		
		<? if (is_object($activeCaptcha)) {
			if ($activeCaptcha->hasOptionsForm()) {
				if ($activeCaptcha->getPackageID() > 0) { 
					Loader::packageElement('system/captcha/' . $activeCaptcha->getSystemCaptchaLibraryHandle() . '/form', $activeCaptcha->getPackageHandle());
				} else {
					Loader::element('system/captcha/' . $activeCaptcha->getSystemCaptchaLibraryHandle() . '/form');
				}
			}
		} ?>


	<? } else { ?>
		<p><?=t('You have no captcha libraries installed.')?></p>
	<? } ?>

<? if (is_object($activeCaptcha) && $activeCaptcha->hasOptionsForm()) { ?>
	</div>
	<div class="ccm-pane-footer">
		<?=Loader::helper('concrete/interface')->submit(t('Save Additional Settings'), 'submit', 'right', 'primary')?>
	</div>
<? } ?>	

	
</form>

<script type="text/javascript">
$(function() {
	$('select[name=activeCaptcha]').change(function() {
		$('#site-form').submit();
	});
});
</script>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper((!is_object($activeCaptcha) || (!$activeCaptcha->hasOptionsForm())));?>
