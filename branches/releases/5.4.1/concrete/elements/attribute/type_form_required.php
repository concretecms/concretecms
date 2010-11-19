<?php  
$form = Loader::helper('form'); 
$ih = Loader::helper("concrete/interface");
$valt = Loader::helper('validation/token');
$akName = '';
$akIsSearchable = 0;
$asID = 0;

if (is_object($key)) {
	if (!isset($akHandle)) {
		$akHandle = $key->getAttributeKeyHandle();
	}
	$akName = $key->getAttributeKeyName();
	$akIsSearchable = $key->isAttributeKeySearchable();
	$akIsSearchableIndexed = $key->isAttributeKeyContentIndexed();
	$sets = $key->getAttributeSets();
	if (count($sets) == 1) {
		$asID = $sets[0]->getAttributeSetID();
	}
	print $form->hidden('akID', $key->getAttributeKeyID());
}
?>
<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader"><?php echo t('Handle')?> <span class="required">*</span></td>
	<td class="subheader"><?php echo t('Name')?> <span class="required">*</span></td>
	<?php  if ($category->allowAttributeSets() == AttributeKeyCategory::ASET_ALLOW_SINGLE) { ?>
		<td class="subheader"><?php echo t("Set")?></td>
	<?php  } ?>
	<td class="subheader"><?php echo t("Searchable")?></td>
</tr>	
<tr>
	<td style="padding-right: 15px"><?php echo $form->text('akHandle', $akHandle, array('style' => 'width: 100%'))?></td>
	<td style="padding-right: 15px"><?php echo $form->text('akName', $akName, array('style' => 'width: 100%'))?></td>
	<?php  if ($category->allowAttributeSets() == AttributeKeyCategory::ASET_ALLOW_SINGLE) { ?>
		<td style="padding-right: 10px">
		<?php 
		$sel = array('0' => t('** None'));
		$sets = $category->getAttributeSets();
		foreach($sets as $as) {
			$sel[$as->getAttributeSetID()] = $as->getAttributeSetName();
		}
		print $form->select('asID', $sel, $asID);
		?>
		</td>
	<?php  } ?>

	<td style="padding-right: 10px">
	<?php echo $form->checkbox('akIsSearchableIndexed', 1, $akIsSearchableIndexed)?> <?php echo t('Content included in "Keyword Search".');?><br/>
	<?php echo $form->checkbox('akIsSearchable', 1, $akIsSearchable)?> <?php echo t('Field available in "Advanced Search".');?>
	</td>
</tr>
</table>

<?php echo $form->hidden('atID', $type->getAttributeTypeID())?>
<?php echo $form->hidden('akCategoryID', $category->getAttributeKeyCategoryID()); ?>
<?php echo $valt->output('add_or_update_attribute')?>
<?php  
if ($category->getPackageID() > 0) { 
	Loader::packageElement('attribute/categories/' . $category->getAttributeKeyCategoryHandle(), $category->getPackageHandle(), array('key' => $key));
} else {
	Loader::element('attribute/categories/' . $category->getAttributeKeyCategoryHandle(), array('key' => $key));
}
?>
<?php  $type->render('type_form', $key); ?>

<?php  if (is_object($key)) { ?>
	<?php echo $ih->submit(t('Update Attribute'), 'ccm-attribute-key-form')?>
<?php  } else { ?>
	<?php echo $ih->submit(t('Add Attribute'), 'ccm-attribute-key-form')?>
<?php  } ?>

<div class="ccm-spacer">&nbsp;</div>