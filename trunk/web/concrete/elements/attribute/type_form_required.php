<? 
$form = Loader::helper('form'); 
$ih = Loader::helper("concrete/interface");
$valt = Loader::helper('validation/token');
$akHandle = '';
$akName = '';
$akIsSearchable = 0;

if (is_object($key)) {
	$akHandle = $key->getAttributeKeyHandle();
	$akName = $key->getAttributeKeyName();
	$akIsSearchable = $key->isAttributeKeySearchable();
	$akIsSearchableIndexed = $key->isAttributeKeyContentIndexed();
	print $form->hidden('akID', $key->getAttributeKeyID());
}
?>
<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader" width="33%"><?=t('Handle')?> <span class="required">*</span></td>
	<td class="subheader" width="34%"><?=t('Name')?> <span class="required">*</span></td>
	<td class="subheader" width="33%"><?=t("Searchable")?></td>
</tr>	
<tr>
	<td style="padding-right: 15px" valign="top"><?=$form->text('akHandle', $akHandle, array('style' => 'width: 100%'))?></td>
	<td style="padding-right: 15px" valign="top"><?=$form->text('akName', $akName, array('style' => 'width: 100%'))?></td>
	<td style="padding-right: 10px" valign="top">
	<?=$form->checkbox('akIsSearchableIndexed', 1, $akIsSearchableIndexed)?> <?=t('Content included in "Keyword Search".');?><br/>
	<?=$form->checkbox('akIsSearchable', 1, $akIsSearchable)?> <?=t('Field available in "Advanced Search".');?>
	</td>
</tr>
</table>

<?=$form->hidden('atID', $type->getAttributeTypeID())?>
<?=$form->hidden('akCategoryID', $category->getAttributeKeyCategoryID()); ?>
<?=$valt->output('add_or_update_attribute')?>
<? 
if ($category->getPackageID() > 0) { 
	Loader::packageElement('attribute/categories/' . $category->getAttributeKeyCategoryHandle(), $category->getPackageHandle(), array('key' => $key));
} else {
	Loader::element('attribute/categories/' . $category->getAttributeKeyCategoryHandle(), array('key' => $key));
}
?>
<? $type->render('type_form', $key); ?>

<? if (is_object($key)) { ?>
	<?=$ih->submit(t('Update Attribute'), 'ccm-attribute-key-form')?>
<? } else { ?>
	<?=$ih->submit(t('Add Attribute'), 'ccm-attribute-key-form')?>
<? } ?>

<div class="ccm-spacer">&nbsp;</div>