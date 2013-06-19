<? defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
Loader::model('attribute/categories/user');
$tp = new TaskPermission();
if (!$tp->canAccessUserSearch()) { 
	die(t("You have no access to users."));
}

Loader::model('user_list');
$selectedAKIDs = array();

$fldc = UserSearchColumnSet::getCurrent();
$fldca = new UserSearchAvailableColumnSet();


$searchInstance = 'user';
if ($_POST['task'] == 'update_columns') {
	
	$fdc = new UserSearchColumnSet();
	foreach($_POST['column'] as $key) {
		$fdc->addColumn($fldca->getColumnByKey($key));
	}	
	$sortCol = $fldca->getColumnByKey($_POST['fSearchDefaultSort']);
	$fdc->setDefaultSortColumn($sortCol, $_POST['fSearchDefaultSortDirection']);
	$u->saveConfig('USER_LIST_DEFAULT_COLUMNS', serialize($fdc));
	
	$userList = new UserList();
	$userList->resetSearchRequest();
	exit;
}

$list = UserAttributeKey::getList();

?>
<div class="ccm-ui">

<form method="post" id="ccm-<?=$searchInstance?>-customize-search-columns-form" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/users/customize_search_columns/">
<?=$form->hidden('task', 'update_columns')?>

	<h3><?=t('Choose Headers')?></h3>
	
	<div class="clearfix">
	<label><?=t('Standard Properties')?></label>
	<div class="input">
	<ul class="inputs-list">
	
	<?
	$columns = $fldca->getColumns();
	foreach($columns as $col) { 

		?>

		<li><label><?=$form->checkbox($col->getColumnKey(), 1, $fldc->contains($col))?> <span><?=$col->getColumnName()?></span></label></li>
	
	<? } ?>
	
	</ul>
	</div>
	</div>

	<div class="clearfix">
	<label><?=t('Additional Attributes')?></label>
	<div class="input">
	<ul class="inputs-list">
	
	<?
	$pk = PermissionKey::getByHandle('view_user_attributes');
	foreach($list as $ak) { 
		if ($pk->validate($ak)) { ?>

		<li><label><?=$form->checkbox('ak_' . $ak->getAttributeKeyHandle(), 1, $fldc->contains($ak))?> <span><?=tc('AttributeKeyName', $ak->getAttributeKeyName())?></span></label></li>
	
	<? } 
	
	}?>
	
	</ul>
	</div>
	</div>
	
	<h3><?=t('Column Order')?></h3>
	
	<p><?=t('Click and drag to change column order.')?></p>
	
	<ul class="ccm-search-sortable-column-wrapper" id="ccm-<?=$searchInstance?>-sortable-column-wrapper">
	<? foreach($fldc->getColumns() as $col) { ?>
		<li id="field_<?=$col->getColumnKey()?>"><input type="hidden" name="column[]" value="<?=$col->getColumnKey()?>" /><?=$col->getColumnName()?></li>	
	<? } ?>	
	</ul>
	
	<br/>
	
	<h3><?=t('Sort By')?></h3>
	
	<div class="ccm-sortable-column-sort-controls">
	
	<? $ds = $fldc->getDefaultSortColumn(); ?>
	
	<select <? if (count($fldc->getSortableColumns()) == 0) { ?>disabled="true"<? } ?> id="ccm-<?=$searchInstance?>-sortable-column-default" name="fSearchDefaultSort">
	<? foreach($fldc->getSortableColumns() as $col) { ?>
		<option id="opt_<?=$col->getColumnKey()?>" value="<?=$col->getColumnKey()?>" <? if ($col->getColumnKey() == $ds->getColumnKey()) { ?> selected="true" <? } ?>><?=$col->getColumnName()?></option>
	<? } ?>	
	</select>
	<select <? if (count($fldc->getSortableColumns()) == 0) { ?>disabled="true"<? } ?> id="ccm-<?=$searchInstance?>-sortable-column-default-direction" name="fSearchDefaultSortDirection">
		<option value="asc" <? if ($ds->getColumnDefaultSortDirection() == 'asc') { ?> selected="true" <? } ?>><?=t('Ascending')?></option>
		<option value="desc" <? if ($ds->getColumnDefaultSortDirection() == 'desc') { ?> selected="true" <? } ?>><?=t('Descending')?></option>	
	</select>	
	</div>

	<div class="dialog-buttons">
	<input type="button" class="btn primary" onclick="$('#ccm-<?=$searchInstance?>-customize-search-columns-form').submit()" value="<?=t('Save')?>" />
	</div>

</form>
</div>

<script type="text/javascript">
ccm_submitCustomizeSearchColumnsForm = function() {
	//ccm_deactivateSearchResults('<?=$searchInstance?>');
	$("#ccm-<?=$searchInstance?>-customize-search-columns-form").ajaxSubmit(function(resp) {
		var sortDirection = $("#ccm-<?=$searchInstance?>-customize-search-columns-form select[name=fSearchDefaultSortDirection]").val();
		var sortCol = $("#ccm-<?=$searchInstance?>-customize-search-columns-form select[name=fSearchDefaultSort]").val();
		$("#ccm-<?=$searchInstance?>-advanced-search input[name=ccm_order_dir]").val(sortDirection);
		$("#ccm-<?=$searchInstance?>-advanced-search input[name=ccm_order_by]").val(sortCol);
		jQuery.fn.dialog.closeTop();
		$("#ccm-<?=$searchInstance?>-advanced-search").ajaxSubmit(function(resp) {
			ccm_parseAdvancedSearchResponse(resp, '<?=$searchInstance?>');
		});
	});
	return false;
}

$(function() {
	$('#ccm-<?=$searchInstance?>-sortable-column-wrapper').sortable({
		cursor: 'move',
		opacity: 0.5
	});
	$('form#ccm-<?=$searchInstance?>-customize-search-columns-form input[type=checkbox]').click(function() {
		var thisLabel = $(this).parent().find('span').html();
		var thisID = $(this).attr('id');
		if ($(this).prop('checked')) {
			if ($('#field_' + thisID).length == 0) {
				$('#ccm-<?=$searchInstance?>-sortable-column-default').append('<option value="' + thisID + '" id="opt_' + thisID + '">' + thisLabel + '<\/option>');
				$('div.ccm-sortable-column-sort-controls select').attr('disabled', false);
				$('#ccm-<?=$searchInstance?>-sortable-column-wrapper').append('<li id="field_' + thisID + '"><input type="hidden" name="column[]" value="' + thisID + '" />' + thisLabel + '<\/li>');
			}
		} else {
			$('#field_' + thisID).remove();
			$('#opt_' + thisID).remove();
			if ($('#ccm-<?=$searchInstance?>-sortable-column-wrapper li').length == 0) {
				$('div.ccm-sortable-column-sort-controls select').attr('disabled', true);
			}
		}
	});
	$('#ccm-<?=$searchInstance?>-customize-search-columns-form').submit(function() {
		return ccm_submitCustomizeSearchColumnsForm();
	});
});


</script>