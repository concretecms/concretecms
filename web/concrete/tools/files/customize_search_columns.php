<? defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
Loader::model('attribute/categories/file');
$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
	die(t("Access Denied."));
}


Loader::model('file_list');
$selectedAKIDs = array();

$fldc = FileManagerColumnSet::getCurrent();
$fldca = new FileManagerAvailableColumnSet();


$searchInstance = $_REQUEST['searchInstance'];
if ($_POST['task'] == 'update_columns') {
	
	$fdc = new FileManagerColumnSet();
	foreach($_POST['column'] as $key) {
		$fdc->addColumn($fldca->getColumnByKey($key));
	}	
	$sortCol = $fldca->getColumnByKey($_POST['fSearchDefaultSort']);
	$fdc->setDefaultSortColumn($sortCol, $_POST['fSearchDefaultSortDirection']);
	$u->saveConfig('FILE_LIST_DEFAULT_COLUMNS', serialize($fdc));
	
	$fileList = new FileList();
	$fileList->resetSearchRequest();
	exit;
}

$list = FileAttributeKey::getList();

?>

<form method="post" id="ccm-<?=$searchInstance?>-customize-search-columns-form" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/customize_search_columns/">
<?=$form->hidden('task', 'update_columns')?>

<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="35%" valign="top">
	<h1><?=t('Choose Headers')?></h1>
	
	<h2><?=t('Standard Properties')?></h2>
	
	<?
	$columns = $fldca->getColumns();
	foreach($columns as $col) { 

		?>

		<div><?=$form->checkbox($col->getColumnKey(), 1, $fldc->contains($col), array('style' => 'vertical-align: middle'))?> <label for="<?=$col->getColumnKey()?>"><?=$col->getColumnName()?></label></div>
	
	<? } ?>
	
	<h2><?=t('Additional Attributes')?></h2>
	
	<? foreach($list as $ak) { ?>
	
		<div><?=$form->checkbox('ak_' . $ak->getAttributeKeyHandle(), 1, $fldc->contains($ak), array('style' => 'vertical-align: middle'))?> <label for="ak_<?=$ak->getAttributeKeyHandle()?>"><?=$ak->getAttributeKeyDisplayHandle()?></label></div>
		
	<? } ?>
	
	</td>
	<td><div style="width: 20px">&nbsp;</div></td>
	<td valign="top" width="65%">
	
	<h1><?=t('Column Order')?></h1>
	
	<p><?=t('Click and drag to change column order.')?></p>
	
	<ul class="ccm-search-sortable-column-wrapper" id="ccm-<?=$searchInstance?>-sortable-column-wrapper">
	<? foreach($fldc->getColumns() as $col) { ?>
		<li id="field_<?=$col->getColumnKey()?>"><input type="hidden" name="column[]" value="<?=$col->getColumnKey()?>" /><?=$col->getColumnName()?></li>	
	<? } ?>	
	</ul>
	
	<h1><?=t('Sort By')?></h1>
	
	<div class="ccm-sortable-column-sort-controls">
	<?
	$h = Loader::helper('concrete/interface');
	$b1 = $h->submit(t('Save'), 'save', 'right');
	print $b1;
	?>

	
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
	
	</td>
</tr>
</table>

</form>

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
		var thisLabel = $(this).parent().find('label').html();
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