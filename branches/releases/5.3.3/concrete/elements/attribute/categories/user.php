<?php 
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
<?php  $form = Loader::helper('form'); ?>
<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader" style="width: 34%"><?php echo t('Public Display')?></td>
	<td class="subheader" style="width: 33%"><?php echo t('Edit Mode')?></td>
	<td class="subheader" style="width: 33%"><?php echo t('Registration')?></td>
</tr>	
<tr>
	<td valign="top">
	<?php echo $form->checkbox('uakProfileDisplay', 1, $uakProfileDisplay)?> <?php echo t('Displayed in Public Profile.');?><br/>
	<?php echo $form->checkbox('uakMemberListDisplay', 1, $uakMemberListDisplay)?> <?php echo t('Displayed on Member List.');?>
	</td>
	<td valign="top">
		<?php echo $form->checkbox('uakProfileEdit', 1, $uakProfileEdit)?> <?php echo t('Editable in Profile.');?><Br/>
		<?php echo $form->checkbox('uakProfileEditRequired', 1, $uakProfileEditRequired)?> <?php echo t('Editable and Required in Profile.');?>
	</td>
	<td valign="top">
		<?php echo $form->checkbox('uakRegisterEdit', 1, $uakRegisterEdit)?> <?php echo t('Show on Registration Form.');?><Br/>
		<?php echo $form->checkbox('uakRegisterEditRequired', 1, $uakRegisterEditRequired)?> <?php echo t('Require on Registration Form.');?>
	</td>
</tr>
</table>

<script type="text/javascript">
$(function() {
	$('input[name=uakProfileEdit]').click(function() {
		if ($(this).attr('checked')) {
			$('input[name=uakProfileEditRequired]').attr('disabled', false);
		} else {
			$('input[name=uakProfileEditRequired]').attr('checked', false);
			$('input[name=uakProfileEditRequired]').attr('disabled', true);		
		}
	});

	$('input[name=uakRegisterEdit]').click(function() {
		if ($(this).attr('checked')) {
			$('input[name=uakRegisterEditRequired]').attr('disabled', false);
		} else {
			$('input[name=uakRegisterEditRequired]').attr('checked', false);
			$('input[name=uakRegisterEditRequired]').attr('disabled', true);		
		}
	});
	

	if ($('input[name=uakProfileEdit]').attr('checked')) {
		$('input[name=uakProfileEditRequired]').attr('disabled', false);
	} else {
		$('input[name=uakProfileEditRequired]').attr('checked', false);
		$('input[name=uakProfileEditRequired]').attr('disabled', true);		
	}	

	if ($('input[name=uakRegisterEdit]').attr('checked')) {
		$('input[name=uakRegisterEditRequired]').attr('disabled', false);
	} else {
		$('input[name=uakRegisterEditRequired]').attr('checked', false);
		$('input[name=uakRegisterEditRequired]').attr('disabled', true);		
	}	

});
</script>