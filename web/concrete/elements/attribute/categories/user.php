<?
if (is_object($key)) {
	$uakProfileDisplay = $key->isAttributeKeyDisplayedOnProfile();
	$uakProfileEdit = $key->isAttributeKeyEditableOnProfile();
	$uakProfileEditRequired = $key->isAttributeKeyRequiredOnProfile();
	$uakRegisterEdit = $key->isAttributeKeyEditableOnRegister();
	$uakRegisterEditRequired = $key->isAttributeKeyRequiredOnRegister();
	$uakMemberListDisplay = $key->isAttributeKeyDisplayedOnMemberList();
	$uakIsActive = $key->isAttributeKeyActive();
}
?>
<? $form = Loader::helper('form'); ?>
<fieldset>
<legend><?=t('User Attribute Options')?></legend>
<div class="clearfix">
<label><?=t('Public Display')?></label>
<div class="input">
<ul class="inputs-list">
	<li><label><?=$form->checkbox('uakProfileDisplay', 1, $uakProfileDisplay)?> <span><?=t('Displayed in Public Profile.');?></span></label></li>
	<li><label><?=$form->checkbox('uakMemberListDisplay', 1, $uakMemberListDisplay)?> <span><?=t('Displayed on Member List.');?></span></label></li>
</ul>
</div>
</div>

<div class="clearfix">
<label><?=t('Edit Mode')?></label>
<div class="input">
<ul class="inputs-list">
	<li><label><?=$form->checkbox('uakProfileEdit', 1, $uakProfileEdit)?> <span><?=t('Editable in Profile.');?></span></label></li>
	<li><label><?=$form->checkbox('uakProfileEditRequired', 1, $uakProfileEditRequired)?> <span><?=t('Editable and Required in Profile.');?></span></label></li>
</ul>
</div>
</div>


<div class="clearfix">
<label><?=t('Registration')?></label>
<div class="input">
<ul class="inputs-list">
	<li><label><?=$form->checkbox('uakRegisterEdit', 1, $uakRegisterEdit)?> <span><?=t('Show on Registration Form.');?></span></label></li>
	<li><label><?=$form->checkbox('uakRegisterEditRequired', 1, $uakRegisterEditRequired)?> <span><?=t('Require on Registration Form.');?></span></label></li>
</ul>
</div>
</div>
</fieldset>

<script type="text/javascript">
$(function() {
	$('input[name=uakProfileEdit]').click(function() {
		if ($(this).prop('checked')) {
			$('input[name=uakProfileEditRequired]').attr('disabled', false);
		} else {
			$('input[name=uakProfileEditRequired]').attr('checked', false);
			$('input[name=uakProfileEditRequired]').attr('disabled', true);		
		}
	});

	$('input[name=uakRegisterEdit]').click(function() {
		if ($(this).prop('checked')) {
			$('input[name=uakRegisterEditRequired]').attr('disabled', false);
		} else {
			$('input[name=uakRegisterEditRequired]').attr('checked', false);
			$('input[name=uakRegisterEditRequired]').attr('disabled', true);		
		}
	});
	

	if ($('input[name=uakProfileEdit]').prop('checked')) {
		$('input[name=uakProfileEditRequired]').attr('disabled', false);
	} else {
		$('input[name=uakProfileEditRequired]').attr('checked', false);
		$('input[name=uakProfileEditRequired]').attr('disabled', true);		
	}	

	if ($('input[name=uakRegisterEdit]').prop('checked')) {
		$('input[name=uakRegisterEditRequired]').attr('disabled', false);
	} else {
		$('input[name=uakRegisterEditRequired]').attr('checked', false);
		$('input[name=uakRegisterEditRequired]').attr('disabled', true);		
	}	

});
</script>