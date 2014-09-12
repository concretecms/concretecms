<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="row">
<div class="span10 offset1">
<div class="page-header">
	<h1><?=t('Site Registration')?></h1>
</div>
</div>
</div>

<?
$attribs = UserAttributeKey::getRegistrationList();

if($success) { ?>
<div class="row">
<div class="span10 offset1">
<?	switch($success) {
		case "registered":
			?>
			<p><strong><?=$successMsg ?></strong><br/><br/>
			<a href="<?=$view->url('/')?>"><?=t('Return to Home')?></a></p>
			<?
		break;
		case "validate":
			?>
			<p><?=$successMsg[0] ?></p>
			<p><?=$successMsg[1] ?></p>
			<p><a href="<?=$view->url('/')?>"><?=t('Return to Home')?></a></p>
			<?
		break;
		case "pending":
			?>
			<p><?=$successMsg ?></p>
			<p><a href="<?=$view->url('/')?>"><?=t('Return to Home')?></a></p>
            <?
		break;
	} ?>
</div>
</div>
<?
} else { ?>
	<form method="post" action="<?=$view->url('/register', 'do_register')?>" class="form-horizontal">
		<div class="row">
			<div class="span10 offset1">
				<fieldset>
					<legend><?=t('Your Details')?></legend>
					<?php
					if ($displayUserName) {
						?>
						<div class="control-group">
							<?=$form->label('uName',t('Username'))?>
							<div class="controls">
								<?=$form->text('uName')?>
							</div>
						</div>
						<?php
					}
					?>
					<div class="control-group">
						<?=$form->label('uEmail',t('Email Address'))?>
						<div class="controls">
							<?=$form->text('uEmail')?>
						</div>
					</div>
					<div class="control-group">
						<?=$form->label('uPassword',t('Password'))?>
						<div class="controls">
							<?=$form->password('uPassword')?>
						</div>
					</div>
					<div class="control-group">
						<?=$form->label('uPasswordConfirm',t('Confirm Password'))?>
						<div class="controls">
							<?=$form->password('uPasswordConfirm')?>
						</div>
					</div>

				</fieldset>
			</div>
		</div>
		<?php
		if (count($attribs) > 0) {
			?>
			<div class="row">
				<div class="span10 offset1">
					<fieldset>
						<legend><?=t('Options')?></legend>
						<?php
						$af = Loader::helper('form/attribute');
						foreach($attribs as $ak) {
							echo $af->display($ak, $ak->isAttributeKeyRequiredOnRegister());
						}
						?>
					</fieldset>
				</div>
			</div>
			<?php
		}
		if (Config::get('concrete.user.registration.captcha')) {
			?>
			<div class="row">
				<div class="span10 offset1 ">

					<div class="control-group">
						<?php
						$captcha = Loader::helper('validation/captcha');
						echo $captcha->label();
						?>
						<div class="controls">
							<?php
							$captcha->showInput();
							$captcha->display();
							?>
						</div>
					</div>

				</div>
			</div>

		<? } ?>
		<div class="row">
			<div class="span10 offset1">
				<div class="actions">
					<?=$form->hidden('rcID', $rcID); ?>
					<?=$form->submit('register', t('Register') . ' &gt;', array('class' => 'primary'))?>
				</div>
			</div>
		</div>
	</form>

	<?php
}
?>
