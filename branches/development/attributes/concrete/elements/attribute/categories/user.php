<?
if (is_object($key)) {
	$uakRequired = $key->isAttributeKeyRequired();
	$uakDisplayedOnRegister = $key->isAttributeKeyDisplayedOnRegister();
	$uakPrivate = $key->isAttributeKeyPrivate();
	$uakHidden = $key->isAttributeKeyHidden();
}
?>

<? $form = Loader::helper('form'); ?>
<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader" style="width: 25%"><?=t('Required')?></td>
	<td class="subheader" style="width: 25%"><?=t('Registration')?></td>
	<td class="subheader" style="width: 25%"><?=t('Private')?></td>
	<td class="subheader" style="width: 25%"><?=t('Hidden')?></td>
</tr>	
<tr>
	<td><?=$form->checkbox('uakRequired', 1, $uakRequired)?> <?=t('Required on Registration page.');?></td>
	<td><?=$form->checkbox('uakDisplayedOnRegister', 1, $uakDisplayedOnRegister)?> <?=t('Displayed on Registration page.');?></td>
	<td><?=$form->checkbox('uakPrivate', 1, $uakPrivate)?> <?=t('Private to user.');?></td>
	<td><?=$form->checkbox('uakHidden', 1, $uakHidden)?> <?=t('Disable attribute. No longer in use.');?></td>
</tr>
</table>