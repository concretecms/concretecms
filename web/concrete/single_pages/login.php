<?php defined('C5_EXECUTE') or die('Access denied.');

$activeAuths = AuthenticationType::getActiveListSorted();
$form = Loader::helper('form');
?>
<style>
.authForm .row {
	margin-left:0;
}
.authForm .actions {
	margin-left:20px;
}
</style>
<div class='row'>
	<div class='span10 offset1'>
		<?php
		if (isset($required_attributes)) {
			$af = Loader::helper('form/attribute');

			?>
			<fieldset>
				<form method="post" action="<?=$view->url('/login', 'fill_attributes')?>">
					<?php
					foreach ($required_attributes as $attribute) {
						?>
						<div class='row'>
							<?php
							echo $af->display($attribute, true);
							?>
						</div>
						<?php
					}
					?>
					<button type="submit" class="btn"><?php echo t('Complete Profile'); ?></button>
				</form>
			</fieldset>
			<?php
		} else {
			?>
			<div class="page-header">
				<h1><?=t('Sign in to %s', SITE)?></h1>
			</div>
			<?php
			// render authentication type specific views
			if($authType instanceof AuthenticationType && strlen($authTypeElement)) {
				$authType->renderForm($authTypeElement);
			} else { // render authentication type(s) initial view
				if (count($activeAuths) > 1) {
					?>
					<ul class="nav nav-tabs">
						<?php
						$first = true;
						foreach ($activeAuths as $auth) {
							?>
							<li<?=$first?" class='active'":''?>>
								<a data-authType='<?=$auth->getAuthenticationTypeHandle()?>' href='#<?=$auth->getAuthenticationTypeHandle()?>'><?=$auth->getAuthenticationTypeName()?></a>
							</li>
							<?php
							$first = false;
						}
						?>
					</ul>
					<?php
				}
				?>
				<div class='authTypes row'>
					<?php
					$first = true;
					foreach ($activeAuths as $auth) {
						?>
						<div data-authType='<?=$auth->getAuthenticationTypeHandle()?>' style='<?=$first?"display:block":"display:none"?>'>
							<fieldset>
								<form method='post' class='form-horizontal' action='<?=$view->url('/login', 'authenticate', $auth->getAuthenticationTypeHandle())?>'>
									<?php $valt->output('login_'.$auth->getAuthenticationTypeHandle());?>
									<div class='authForm'>
										<?$auth->renderForm()?>
									</div>
								</form>
							</fieldset>
						</div>
						<?php
						$first = false;
					}
					?>
				</div>
				<?php
			}
		}
		?>
	</div>
</div>
<script type="text/javascript">
(function($){
	"use strict";
	$('ul.nav.nav-tabs > li > a').on('click',function(){
		var me = $(this);
		if (me.parent().hasClass('active')) return false;
		$('ul.nav.nav-tabs > li.active').removeClass('active');
		var at = me.attr('data-authType');
		me.parent().addClass('active');
		$('div.authTypes > div').hide().filter('[data-authType="'+at+'"]').show();
		return false;
	});
})(jQuery);
</script>
