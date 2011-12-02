<? defined('C5_EXECUTE') or die("Access Denied.");?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Captcha Settings'), false, 'span12 offset2')?>
<form method="post" id="site-form" action="<?=$this->action('update_captcha')?>">
	<?=$this->controller->token->output('update_captcha')?>
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
			?>
			
			<div class="well">
				<?=Loader::helper('concrete/interface')->submit(t('Save Settings'), 'submit', 'right', 'primary')?>
			</div>
			
			<?
		}
	} ?>

</form>

<script type="text/javascript">
$(function() {
	$('select[name=activeCaptcha]').change(function() {
		$('#site-form').submit();
	});
});
</script>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
