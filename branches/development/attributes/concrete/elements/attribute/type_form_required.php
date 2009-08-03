<? 
$form = Loader::helper('form'); 
$ih = Loader::helper("concrete/interface");
$valt = Loader::helper('validation/token');
$akHandle = '';
$akName = '';
$akIsSearchable = 0;

if (is_object($ak)) {
	$akHandle = $ak->getAttributeKeyHandle();
	$akName = $ak->getAttributeKeyName();
	$akIsSearchable = $ak->isAttributeKeySearchable();
}
?>
<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader"><?=t('Handle')?> <span class="required">*</span></td>
	<td class="subheader"><?=t('Name')?> <span class="required">*</span></td>
	<td class="subheader"><?=t("Searchable")?></td>
</tr>	
<tr>
	<td style="padding-right: 15px"><?=$form->text('akHandle', $akHandle, array('style' => 'width: 100%'))?></td>
	<td style="padding-right: 15px"><?=$form->text('akName', $akName, array('style' => 'width: 100%'))?></td>
	<td style="padding-right: 10px"><?=$form->checkbox('akIsSearchable', 1, $akIsSearchable)?> <?=t('Yes, include this field in the search index.');?></td>
</tr>
</table>

<?=$form->hidden('atID', $type->getAttributeTypeID())?>
<?=$form->hidden('akCategoryID', $category->getAttributeKeyCategoryID()); ?>
<?=$valt->output('add_or_update_attribute')?>
<? $type->render('type_form'); ?>

<?=$ih->submit(t('Add Attribute'), 'ccm-attribute-key-form')?>

<div class="ccm-spacer">&nbsp;</div>