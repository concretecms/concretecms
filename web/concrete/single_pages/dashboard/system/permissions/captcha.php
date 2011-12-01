<? defined('C5_EXECUTE') or die("Access Denied.");?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Captcha Settings'), false, 'span12 offset2')?>
<form method="post" id="site-form" action="<?=$this->action('update_captcha')?>">
<div class="ccm-pane-body">
	<?=$this->controller->token->output('update_captcha')?>
	<div class="clearfix">
	<?=$form->label('activeCaptcha', t('Active Captcha'))?>
	<div class="input">
	<?=$form->select('activeCaptcha', $captchas, $activeCaptcha, array('class' => 'span4'))?>
	</div>
	</div>
</div>
</form>

<script type="text/javascript">
$(function() {
	$('select[name=activeCaptcha]').change(function() {
		$('#site-form').submit();
	});
});
</script>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
