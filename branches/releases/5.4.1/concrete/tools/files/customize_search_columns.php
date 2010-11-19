<?php  defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
Loader::model('attribute/categories/file');
$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
	die(_("Access Denied."));
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

<form method="post" id="ccm-<?php echo $searchInstance?>-customize-search-columns-form" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/customize_search_columns/">
<?php echo $form->hidden('task', 'update_columns')?>

<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="35%" valign="top">
	<h1><?php echo t('Choose Headers')?></h1>
	
	<h2><?php echo t('Standard Properties')?></h2>
	
	<?php 
	$columns = $fldca->getColumns();
	foreach($columns as $col) { 

		?>

		<div><?php echo $form->checkbox($col->getColumnKey(), 1, $fldc->contains($col), array('style' => 'vertical-align: middle'))?> <label for="<?php echo $col->getColumnKey()?>"><?php echo $col->getColumnName()?></label></div>
	
	<?php  } ?>
	
	<h2><?php echo t('Additional Attributes')?></h2>
	
	<?php  foreach($list as $ak) { ?>
	
		<div><?php echo $form->checkbox('ak_' . $ak->getAttributeKeyHandle(), 1, $fldc->contains($ak), array('style' => 'vertical-align: middle'))?> <label for="ak_<?php echo $ak->getAttributeKeyHandle()?>"><?php echo $ak->getAttributeKeyDisplayHandle()?></label></div>
		
	<?php  } ?>
	
	</td>
	<td><div style="width: 20px">&nbsp;</div></td>
	<td valign="top" width="65%">
	
	<h1><?php echo t('Column Order')?></h1>
	
	<p><?php echo t('Click and drag to change column order.')?></p>
	
	<ul class="ccm-search-sortable-column-wrapper" id="ccm-<?php echo $searchInstance?>-sortable-column-wrapper">
	<?php  foreach($fldc->getColumns() as $col) { ?>
		<li id="field_<?php echo $col->getColumnKey()?>"><input type="hidden" name="column[]" value="<?php echo $col->getColumnKey()?>" /><?php echo $col->getColumnName()?></li>	
	<?php  } ?>	
	</ul>
	
	<h1><?php echo t('Sort By')?></h1>
	
	<div class="ccm-sortable-column-sort-controls">
	<?php 
	$h = Loader::helper('concrete/interface');
	$b1 = $h->submit(t('Save'), 'save', 'right');
	print $b1;
	?>

	
	<?php  $ds = $fldc->getDefaultSortColumn(); ?>
	
	<select <?php  if (count($fldc->getSortableColumns()) == 0) { ?>disabled="true"<?php  } ?> id="ccm-<?php echo $searchInstance?>-sortable-column-default" name="fSearchDefaultSort">
	<?php  foreach($fldc->getSortableColumns() as $col) { ?>
		<option id="opt_<?php echo $col->getColumnKey()?>" value="<?php echo $col->getColumnKey()?>" <?php  if ($col->getColumnKey() == $ds->getColumnKey()) { ?> selected="true" <?php  } ?>><?php echo $col->getColumnName()?></option>
	<?php  } ?>	
	</select>
	<select <?php  if (count($fldc->getSortableColumns()) == 0) { ?>disabled="true"<?php  } ?> id="ccm-<?php echo $searchInstance?>-sortable-column-default-direction" name="fSearchDefaultSortDirection">
		<option value="asc" <?php  if ($ds->getColumnDefaultSortDirection() == 'asc') { ?> selected="true" <?php  } ?>><?php echo t('Ascending')?></option>
		<option value="desc" <?php  if ($ds->getColumnDefaultSortDirection() == 'desc') { ?> selected="true" <?php  } ?>><?php echo t('Descending')?></option>	
	</select>	
	</div>
	
	</td>
</tr>
</table>

</form>

<script type="text/javascript">
ccm_submitCustomizeSearchColumnsForm = function() {
	//ccm_deactivateSearchResults('<?php echo $searchInstance?>');
	$("#ccm-<?php echo $searchInstance?>-customize-search-columns-form").ajaxSubmit(function(resp) {
		var sortDirection = $("#ccm-<?php echo $searchInstance?>-customize-search-columns-form select[name=fSearchDefaultSortDirection]").val();
		var sortCol = $("#ccm-<?php echo $searchInstance?>-customize-search-columns-form select[name=fSearchDefaultSort]").val();
		$("#ccm-<?php echo $searchInstance?>-advanced-search input[name=ccm_order_dir]").val(sortDirection);
		$("#ccm-<?php echo $searchInstance?>-advanced-search input[name=ccm_order_by]").val(sortCol);
		jQuery.fn.dialog.closeTop();
		$("#ccm-<?php echo $searchInstance?>-advanced-search").ajaxSubmit(function(resp) {
			ccm_parseAdvancedSearchResponse(resp, '<?php echo $searchInstance?>');
		});
	});
	return false;
}

$(function() {
	$('#ccm-<?php echo $searchInstance?>-sortable-column-wrapper').sortable({
		cursor: 'move',
		opacity: 0.5
	});
	$('form#ccm-<?php echo $searchInstance?>-customize-search-columns-form input[type=checkbox]').click(function() {
		var thisLabel = $(this).parent().find('label').html();
		var thisID = $(this).attr('id');
		if ($(this).attr('checked')) {
			if ($('#field_' + thisID).length == 0) {
				$('#ccm-<?php echo $searchInstance?>-sortable-column-default').append('<option value="' + thisID + '" id="opt_' + thisID + '">' + thisLabel + '<\/option>');
				$('div.ccm-sortable-column-sort-controls select').attr('disabled', false);
				$('#ccm-<?php echo $searchInstance?>-sortable-column-wrapper').append('<li id="field_' + thisID + '"><input type="hidden" name="column[]" value="' + thisID + '" />' + thisLabel + '<\/li>');
			}
		} else {
			$('#field_' + thisID).remove();
			$('#opt_' + thisID).remove();
			if ($('#ccm-<?php echo $searchInstance?>-sortable-column-wrapper li').length == 0) {
				$('div.ccm-sortable-column-sort-controls select').attr('disabled', true);
			}
		}
	});
	$('#ccm-<?php echo $searchInstance?>-customize-search-columns-form').submit(function() {
		return ccm_submitCustomizeSearchColumnsForm();
	});
});


</script>