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
<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader" style="width: 34%"><?=t('Public Display')?></td>
	<td class="subheader" style="width: 33%"><?=t('Edit Mode')?></td>
	<td class="subheader" style="width: 33%"><?=t('Registration')?></td>
</tr>	
<tr>
	<td valign="top">
	<?=$form->checkbox('uakProfileDisplay', 1, $uakProfileDisplay)?> <?=t('Displayed in Public Profile.');?><br/>
	<?=$form->checkbox('uakMemberListDisplay', 1, $uakMemberListDisplay)?> <?=t('Displayed on Member List.');?>
	</td>
	<td valign="top">
		<?=$form->checkbox('uakProfileEdit', 1, $uakProfileEdit)?> <?=t('Editable in Profile.');?><Br/>
		<?=$form->checkbox('uakProfileEditRequired', 1, $uakProfileEditRequired)?> <?=t('Editable and Required in Profile.');?>
	</td>
	<td valign="top">
		<?=$form->checkbox('uakRegisterEdit', 1, $uakRegisterEdit)?> <?=t('Show on Registration Form.');?><Br/>
		<?=$form->checkbox('uakRegisterEditRequired', 1, $uakRegisterEditRequired)?> <?=t('Require on Registration Form.');?>
	</td>
</tr>
</table>

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