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
			<option value="download"><?=t('Download')?></option>
			<option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Sets')?>" data-bulk-action-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/add_to" data-bulk-action-dialog-width="500" data-bulk-action-dialog-height="400"><?=t('Sets')?></option>
			<option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Properties')?>" data-bulk-action-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/bulk_properties" data-bulk-action-dialog-width="690" data-bulk-action-dialog-height="440"><?=t('Properties')?></option>
			<option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Rescan')?>" data-bulk-action-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/rescan" data-bulk-action-dialog-width="350" data-bulk-action-dialog-height="200"><?=t('Rescan')?></option>
			<option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Duplicate')?>" data-bulk-action-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/duplicate" data-bulk-action-dialog-width="500" data-bulk-action-dialog-height="400"><?=t('Copy')?></option>
			<option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Delete')?>" data-bulk-action-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/delete" data-bulk-action-dialog-width="500" data-bulk-action-dialog-height="400"><?=t('Delete')?></option>
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
	<?
	$s1 = FileSet::getMySets();
	if (count($s1) > 0) { ?>
	<div class="ccm-search-fields-row">
		<div class="form-group">
		<?=$form->label('fsID', t('File Set'))?>
		<div class="ccm-search-field-content">			
		<select multiple name="fsID[]" class="chosen-select">
			<optgroup label="<?=t('Sets')?>">
			<? foreach($s1 as $s) { ?>
				<option value="<?=$s->getFileSetID()?>"  <? if (is_array($searchRequest['fsID']) && in_array($s->getFileSetID(), $searchRequest['fsID'])) { ?> selected="selected" <? } ?>><?=wordwrap($s->getFileSetName(), '23', '&shy;', true)?></option>
			<? } ?>
			</optgroup>
			<optgroup label="<?=t('Other')?>">
				<option value="-1" <? if (is_array($searchRequest['fsID']) && in_array(-1, $searchRequest['fsID'])) { ?> selected="selected" <? } ?>><?=t('Files in no sets.')?></option>
			</optgroup>
		</select>
		</div>
		</div>
	</div>
	<? } ?>
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
<tr data-launch-search-menu="<%=file.fID%>" data-file-manager-file="<%=file.fID%>">
	<td><span class="ccm-search-results-checkbox"><input type="checkbox" data-search-checkbox="individual" value="<%=file.fID%>" /></span></td>
	<td class="ccm-file-manager-search-results-star <% if (file.isStarred) { %>ccm-file-manager-search-results-star-active<% } %>"><a href="#" data-search-toggle="star" data-search-toggle-url="<?=URL::to('/system/file/star')?>" data-search-toggle-file-id="<%=file.fID%>"><i class="glyphicon glyphicon-star"></i></a></td>
	<td class="ccm-file-manager-search-results-thumbnail"><img src="<%=file.thumbnailLevel1%>" /></td>
	<% for(i = 0; i < file.columns.length; i++) {
		var column = file.columns[i]; %>
		<td><%=column.value%></td>
	<% } %>
</tr>
<% }); %>
</script>

<div data-search-element="wrapper"></div>

<div data-search-element="results">

<table border="0" cellspacing="0" cellpadding="0" class="ccm-search-results-table">
<thead>
</thead>
<tbody>
</tbody>
</table>

<div class="ccm-search-results-pagination"></div>

</div>

<script type="text/template" data-template="search-results-pagination">
<ul class="pagination">
	<li class="<%=pagination.prevClass%>"><%=pagination.previousPage%></li>
	<%=pagination.pages%>
	<li class="<%=pagination.nextClass%>"><%=pagination.nextPage%></li>
</div>
</script>

<script type="text/template" data-template="search-results-table-head">
<tr>
	<th><span class="ccm-search-results-checkbox"><input type="checkbox" data-search-checkbox="select-all" /></span></th>
	<th class="ccm-file-manager-search-results-star"><span><i class="glyphicon glyphicon-star"></i></span></th>
	<th><span><?=t('Thumbnail')?></th>
	<% 
	for (i = 0; i < columns.length; i++) {
		var column = columns[i];
		if (column.isColumnSortable) { %>
			<th class="<%=column.className%>"><a href="<%=column.sortURL%>"><%=column.title%></a></th>
		<% } else { %>
			<th><span><%=column.title%></span></th>
		<% } %>
	<% } %>
</tr>
</script>
