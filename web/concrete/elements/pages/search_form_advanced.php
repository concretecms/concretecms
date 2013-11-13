<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?
$form = Loader::helper('form');

$searchFields = array(
	'keywords' => t('Full Page Index'),
	'date_added' => t('Date Added'),
	'theme' => t('Theme'),
	'last_modified' => t('Last Modified'),
	'date_public' => t('Public Date'),
	'owner' => t('Page Owner'),
	'num_children' => t('# Children'),
	'version_status' => t('Approved Version')
);

if (PERMISSIONS_MODEL != 'simple') {
	$searchFields['permissions_inheritance'] = t('Permissions Inheritance');
}

if (!$searchDialog) {
	$searchFields['parent'] = t('Parent Page');
}

Loader::model('attribute/categories/collection');
$searchFieldAttributes = CollectionAttributeKey::getSearchableList();
foreach($searchFieldAttributes as $ak) {
	$searchFields[$ak->getAttributeKeyID()] = tc('AttributeKeyName', $ak->getAttributeKeyName());
}

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

<form role="form" data-search-form="pages" action="<?=URL::to('/system/search/pages/submit')?>" class="form-inline ccm-search-fields">
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
			<button type="submit" class="ccm-search-field-hidden-submit" tabindex="-1"><?=t('Search')?></button>
		</div>
	</div>
	<ul class="ccm-search-form-advanced list-inline">
		<li><a href="#" data-search-toggle="advanced"><?=t('Advanced Search')?></a>
		<li><a href="#" data-search-toggle="customize" data-search-column-customize-url="<?=URL::to('/system/dialogs/search/pages/customize')?>"><?=t('Customize Results')?></a>
	</ul>
	<div class="ccm-search-fields-advanced"></div>
</form>

<script type="text/template" data-template="search-field-row">
<div class="ccm-search-fields-row">
	<select name="field[]" class="ccm-search-choose-field" data-search-field="pages">
		<option value=""><?=t('Choose Field')?></option>
		<? foreach($searchFields as $key => $value) { ?>
			<option value="<?=$key?>" <% if (typeof(field) != 'undefined' && field.field == '<?=$key?>') { %>selected<% } %> data-search-field-url="<?=URL::to('/system/search/pages/field', $key)?>"><?=$value?></option>
		<? } ?>
	</select>
	<div class="ccm-search-field-content"><% if (typeof(field) != 'undefined') { %><%=field.html%><% } %></div>
	<a data-search-remove="search-field" class="ccm-search-remove-field" href="#"><i class="glyphicon glyphicon-minus-sign"></i></a>
</div>
</script>

