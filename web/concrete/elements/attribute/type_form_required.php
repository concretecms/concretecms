<? 
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
	<td class="subheader"><?=t('Handle')?> <span class="required">*</span></td>
	<td class="subheader"><?=t('Name')?> <span class="required">*</span></td>
	<? if ($category->allowAttributeSets() == AttributeKeyCategory::ASET_ALLOW_SINGLE) { ?>
		<td class="subheader"><?=t("Set")?></td>
	<? } ?>
	<td class="subheader"><?=t("Searchable")?></td>
</tr>	
<tr>
	<td style="padding-right: 15px"><?=$form->text('akHandle', $akHandle, array('style' => 'width: 100%'))?></td>
	<td style="padding-right: 15px"><?=$form->text('akName', $akName, array('style' => 'width: 100%'))?></td>
	<? if ($category->allowAttributeSets() == AttributeKeyCategory::ASET_ALLOW_SINGLE) { ?>
		<td style="padding-right: 10px">
		<?
		$sel = array('0' => t('** None'));
		$sets = $category->getAttributeSets();
		foreach($sets as $as) {
			$sel[$as->getAttributeSetID()] = $as->getAttributeSetName();
		}
		print $form->select('asID', $sel, $asID);
		?>
		</td>
	<? } ?>

	<td style="padding-right: 10px">
	<?php
	$category_handle = $category->getAttributeKeyCategoryHandle();
	$keyword_label = t('Content included in "Keyword Search".');
	$advanced_label = t('Field available in "Advanced Search".');
	switch ($category_handle) {
		case 'page':
			$keyword_label = t('Content included in sitewide search".');
			$advanced_label = t('Field available in Dashboard Page Search".');
			break;
		case 'file':
			$keyword_label = t('Content included in sitewide search".');
			$advanced_label = t('Field available in File Manager Search".');			
			break;
		case 'user':
			$keyword_label = t('Content included in sitewide search".');
			$advanced_label = t('Field available in Dashboard User Search".');
			break;
	}
	?>
	<?=$form->checkbox('akIsSearchableIndexed', 1, $akIsSearchableIndexed)?> <?=$keyword_label?><br/>
	<?=$form->checkbox('akIsSearchable', 1, $akIsSearchable)?> <?=$advanced_label?>
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