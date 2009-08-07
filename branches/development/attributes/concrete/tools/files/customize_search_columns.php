<? defined('C5_EXECUTE') or die(_("Access Denied."));
$u = new User();
$form = Loader::helper('form');
Loader::model('attribute/categories/file');
$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
	die(_("Access Denied."));
}

$selectedAKIDs = array();
$slist = FileAttributeKey::getColumnHeaderList();
foreach($slist as $sk) {
	$selectedAKIDs[] = $sk->getAttributeKeyID();
}

if ($_POST['task'] == 'update_columns') {
	Loader::model('attribute/category');
	$sc = AttributeKeyCategory::getByHandle('file');
	$sc->clearAttributeKeyCategoryColumnHeaders();
	
	if (is_array($_POST['akID'])) {
		foreach($_POST['akID'] as $akID) {
			$ak = FileAttributeKey::getByID($akID);
			$ak->setAttributeKeyColumnHeader(1);
		}
	}
	
	exit;
}

$list = FileAttributeKey::getList();

?>

<form method="post" id="ccm-file-customize-search-columns-form" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/customize_search_columns/">
<?=$form->hidden('task', 'update_columns')?>

<h1><?=t('Additional Searchable Attributes')?></h1>

<p><?=t('Choose the additional attributes you wish to include as column headers.')?></p>

<? foreach($list as $ak) { ?>

	<div><?=$form->checkbox('akID[]', $ak->getAttributeKeyID(), in_array($ak->getAttributeKeyID(), $selectedAKIDs), array('style' => 'vertical-align: middle'))?> <?=$ak->getAttributeKeyName()?></div>
	
<? } ?>

<br/><br/>
<?
$h = Loader::helper('concrete/interface');
$b1 = $h->button_js(t('Save'), 'ccm_alSubmitCustomizeSearchColumnsForm()', 'left');
print $b1;
?>

</form>

<script type="text/javascript">
ccm_alSubmitCustomizeSearchColumnsForm = function() {
	ccm_deactivateSearchResults();
	$("#ccm-file-customize-search-columns-form").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		$(".ccm-dashboard-file-search").ajaxSubmit(function(resp) {
			ccm_alParseSearchResponse(resp);
		});
	});
}

$(function() {
	$('#ccm-file-customize-search-columns-form').submit(function() {
		ccm_alSubmitCustomizeSearchColumnsForm();
	});
});


</script>