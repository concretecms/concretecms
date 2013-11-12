<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?
$form = Loader::helper('form');

if (PERMISSIONS_MODEL != 'simple') {
	$searchFields['permissions_inheritance'] = t('Permissions Inheritance');
}

if (!$searchDialog) {
	$searchFields['parent'] = t('Parent Page');
}

$searchFieldAttributes = CollectionAttributeKey::getSearchableList();
foreach($searchFieldAttributes as $ak) {
	$searchFields[$ak->getAttributeKeyID()] = tc('AttributeKeyName', $ak->getAttributeKeyName());
}

?>

<form role="form" data-search-form="pages" action="<?=REL_DIR_FILES_TOOLS_REQUIRED . '/pages/search_results'?>" class="form-inline ccm-search-fields">
	<div class="form-group">
		<select data-bulk-action="pages" class="ccm-search-bulk-action form-control">
			<option value=""><?=t('Items Selected')?></option>
			<option value="properties"><?=t('Edit Properties')?></option>
			<option value="move_copy"><?=t('Move/Copy')?></option>
			<option value="speed_settings"><?=t('Speed Settings')?></option>
			<? if (PERMISSIONS_MODEL == 'advanced') { ?>
				<option value="permissions"><?=t('Change Permissions')?></option>
				<option value="permissions_add_access"><?=t('Change Permissions - Add Access')?></option>
				<option value="permissions_remove_access"><?=t('Change Permissions - Remove Access')?></option>
			<? } ?>
			<option value="design"><?=t('Design')?></option>
			<option value="delete"><?=t('Delete')?></option>
		</select>	
	</div>
	<div class="form-group">
		<div class="ccm-search-main-lookup-field">
			<i class="glyphicon glyphicon-search"></i>
			<?=$form->search('cvName', $searchRequest['cvName'], array('placeholder' => t('Page Name')))?>
		</div>
	</div>
	<div class="form-group">
		<a href="#" class="ccm-search-advanced-label" data-search-toggle="advanced"><?=t('Advanced Search')?></a>
	</div>
</form>

