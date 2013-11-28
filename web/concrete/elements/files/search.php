<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');

$searchFields = array(
	'size' => t('Size'),
	'type' => t('Type'),
	'extension' => t('Extension'),
	'date_added' => t('Added Between'),
	'added_to' => t('Added to Page')
);

if (PERMISSIONS_MODEL != 'simple') {
	$searchFields['permissions_inheritance'] = t('Permissions Inheritance');
}

$searchFieldAttributes = FileAttributeKey::getSearchableList();
foreach($searchFieldAttributes as $ak) {
	$searchFields[$ak->getAttributeKeyID()] = tc('AttributeKeyName', $ak->getAttributeKeyName());
}

$searchRequest = $controller->getSearchRequest();

?>

<script type="text/template" data-template="search-form">
<form role="form" data-search-form="files" action="<?=URL::to('/system/search/files/submit')?>" class="form-inline ccm-search-fields">
	<div class="ccm-search-fields-row">
	<div class="form-group">
		<select data-bulk-action="files" disabled class="ccm-search-bulk-action form-control">
			<option value=""><?=t('Items Selected')?></option>
			<option data-bulk-action-type="download"><?=t('Download')?></option>
			<option><?=t('Sets')?></option>
			<option><?=t('Properties')?></option>
			<option><?=t('Rescan')?></option>
			<option><?=t('Copy')?></option>
			<option><?=t('Delete')?></option>
		</select>	
	</div>
	<div class="form-group">
		<div class="ccm-search-main-lookup-field">
			<i class="glyphicon glyphicon-search"></i>
			<?=$form->search('fKeywords', $searchRequest['fKeywords'], array('placeholder' => t('Keywords')))?>
			<button type="submit" class="ccm-search-field-hidden-submit" tabindex="-1"><?=t('Search')?></button>
		</div>
	</div>
	<ul class="ccm-search-form-advanced list-inline">
		<li><a href="#" data-search-toggle="advanced"><?=t('Advanced Search')?></a>
		<li><a href="#" data-search-toggle="customize" data-search-column-customize-url="<?=URL::to('/system/dialogs/file/search/customize')?>"><?=t('Customize Results')?></a>
	</ul>
	</div>
	<div class="ccm-search-fields-advanced"></div>
</form>
</script>

<script type="text/template" data-template="search-field-row">
<div class="ccm-search-fields-row">
	<select name="field[]" class="ccm-search-choose-field" data-search-field="files">
		<option value=""><?=t('Choose Field')?></option>
		<? foreach($searchFields as $key => $value) { ?>
			<option value="<?=$key?>" <% if (typeof(field) != 'undefined' && field.field == '<?=$key?>') { %>selected<% } %> data-search-field-url="<?=URL::to('/system/search/files/field', $key)?>"><?=$value?></option>
		<? } ?>
	</select>
	<div class="ccm-search-field-content"><% if (typeof(field) != 'undefined') { %><%=field.html%><% } %></div>
	<a data-search-remove="search-field" class="ccm-search-remove-field" href="#"><i class="glyphicon glyphicon-minus-sign"></i></a>
</div>
</script>

<script type="text/template" data-template="search-results-table-body">
<% _.each(items, function(file) {%>
<tr data-launch-search-menu="<%=file.fID%>">
	<td><span class="ccm-search-results-checkbox"><input type="checkbox" data-search-checkbox="individual" value="<%=file.fID%>" /></span></td>
	<% for(i = 0; i < file.columns.length; i++) {
		var column = file.columns[i]; %>
		<td><%=column.value%></td>
	<% } %>
</tr>
<% }); %>
</script>

<script type="text/template" data-template="search-results-menu">
<div class="popover fade" data-search-menu="<%=item.fID%>">
	<div class="arrow"></div>
	<div class="popover-inner">
	<ul class="dropdown-menu">
		<li><a href="">derb</a></li>
	</ul>
</div>
</script>

<? Loader::element('search/template')?>