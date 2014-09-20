<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');

$searchFields = array(
    'parent' => t('Parent Page'),
    'keywords' => t('Full Page Index'),
    'date_added' => t('Date Added'),
    'theme' => t('Theme'),
    'last_modified' => t('Last Modified'),
    'date_public' => t('Public Date'),
    'owner' => t('Page Owner'),
    'num_children' => t('# Children'),
    'version_status' => t('Approved Version')
);

if (Config::get('concrete.permissions.model') != 'simple') {
    $searchFields['permissions_inheritance'] = t('Permissions Inheritance');
}

$searchFieldAttributes = CollectionAttributeKey::getSearchableList();
foreach ($searchFieldAttributes as $ak) {
    $searchFields[$ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayName();
}

$flr = new \Concrete\Core\Search\StickyRequest('pages');
$req = $flr->getSearchRequest();

?>

<script type="text/template" data-template="search-form">
<form role="form" data-search-form="pages" action="<?=URL::to('/ccm/system/search/pages/submit')?>" class="form-inline ccm-search-fields">
	<div class="ccm-search-fields-row">
	<div class="form-group">
		<select data-bulk-action="pages" disabled class="ccm-search-bulk-action form-control">
			<option value=""><?=t('Items Selected')?></option>
			<option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Page Properties')?>" data-bulk-action-url="<?=URL::to('/ccm/system/dialogs/page/bulk/properties')?>" data-bulk-action-dialog-width="630" data-bulk-action-dialog-height="450"><?=t('Edit Properties')?></option>
			<option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Move/Copy')?>" data-bulk-action-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_search_selector" data-bulk-action-dialog-width="90%" data-bulk-action-dialog-height="70%"><?=t('Move/Copy')?></option>
<? /*	    <option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Speed Settings')?>" data-bulk-action-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/speed_settings" data-bulk-action-dialog-width="610" data-bulk-action-dialog-height="340"><?=t('Speed Settings')?></option>
			<?php if (Config::get('concrete.permissions.model') == 'advanced') { ?>
				<option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Change Permissions')?>" data-bulk-action-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/permissions" data-bulk-action-dialog-width="430" data-bulk-action-dialog-height="630"><?=t('Change Permissions')?></option>
				<option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Change Permissions - Add Access')?>" data-bulk-action-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/permissions_access?task=add" data-bulk-action-dialog-width="440" data-bulk-action-dialog-height="200"><?=t('Change Permissions - Add Access')?></option>
				<option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Change Permissions - Remove Access')?>" data-bulk-action-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/permissions_access?task=remove" data-bulk-action-dialog-width="440" data-bulk-action-dialog-height="300"><?=t('Change Permissions - Remove Access')?></option>
			<?php } ?>
			<option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Design')?>" data-bulk-action-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/design" data-bulk-action-dialog-width="610" data-bulk-action-dialog-height="405"><?=t('Design')?></option>
 */ ?>
			<option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Delete')?>" data-bulk-action-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/delete" data-bulk-action-dialog-width="500" data-bulk-action-dialog-height="400"><?=t('Delete')?></option>
		</select>
	</div>
	<div class="form-group">
		<div class="ccm-search-main-lookup-field">
			<i class="fa fa-search"></i>
			<?=$form->search('cvName', $req['cvName'], array('placeholder' => t('Page Name')))?>
			<button type="submit" class="ccm-search-field-hidden-submit" tabindex="-1"><?=t('Search')?></button>
		</div>
	</div>
	<ul class="ccm-search-form-advanced list-inline">
		<li><a href="#" data-search-toggle="advanced"><?=t('Advanced Search')?></a>
		<li><a href="#" data-search-toggle="customize" data-search-column-customize-url="<?=URL::to('/ccm/system/dialogs/page/search/customize')?>"><?=t('Customize Results')?></a>
	</ul>
	</div>
	<div class="ccm-search-fields-advanced"></div>
</form>
</script>

<script type="text/template" data-template="search-field-row">
<div class="ccm-search-fields-row">
	<select name="field[]" class="ccm-search-choose-field" data-search-field="pages">
		<option value=""><?=t('Choose Field')?></option>
		<?php foreach ($searchFields as $key => $value) { ?>
			<option value="<?=$key?>" <% if (typeof(field) != 'undefined' && field.field == '<?=$key?>') { %>selected<% } %> data-search-field-url="<?=URL::to('/ccm/system/search/pages/field', $key)?>"><?=$value?></option>
		<?php } ?>
	</select>
	<div class="ccm-search-field-content"><% if (typeof(field) != 'undefined') { %><%=field.html%><% } %></div>
	<a data-search-remove="search-field" class="ccm-search-remove-field" href="#"><i class="fa fa-minus-circle"></i></a>
</div>
</script>

<script type="text/template" data-template="search-results-table-body">
<% _.each(items, function (page) {%>
<tr data-launch-search-menu="<%=page.cID%>" data-page-id="<%=page.cID%>" data-page-name="<%=page.cvName%>">
	<td><span class="ccm-search-results-checkbox"><input type="checkbox" class="ccm-flat-checkbox" data-search-checkbox="individual" value="<%=page.cID%>" /></span></td>
	<% for (i = 0; i < page.columns.length; i++) {
		var column = page.columns[i];
		if (column.key == 'cvName') { %>
			<td class="ccm-search-results-name"><%=column.value%></td>
		<% } else { %>
			<td><%=column.value%></td>
		<% } %>
	<% } %>
</tr>
<% }); %>
</script>

<?php Loader::element('search/template')?>
