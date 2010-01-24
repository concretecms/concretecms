<? defined('C5_EXECUTE') or die(_("Access Denied."));
$u = new User();
$form = Loader::helper('form');
Loader::model('attribute/categories/collection');

$selectedAKIDs = array();
$slist = CollectionAttributeKey::getColumnHeaderList();
foreach($slist as $sk) {
	$selectedAKIDs[] = $sk->getAttributeKeyID();
}

if ($_POST['task'] == 'update_columns') {
	Loader::model('attribute/category');
	$sc = AttributeKeyCategory::getByHandle('collection');
	$sc->clearAttributeKeyCategoryColumnHeaders();
	
	if (is_array($_POST['akID'])) {
		foreach($_POST['akID'] as $akID) {
			$ak = CollectionAttributeKey::getByID($akID);
			$ak->setAttributeKeyColumnHeader(1);
		}
	}
	
	exit;
}

$list = CollectionAttributeKey::getList();

?>

<form method="post" id="ccm-page-customize-search-columns-form" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/customize_search_columns/">
<?=$form->hidden('task', 'update_columns')?>

<h1><?=t('Additional Searchable Attributes')?></h1>

<p><?=t('Choose the additional attributes you wish to include as column headers.')?></p>

<? foreach($list as $ak) { ?>

	<div><?=$form->checkbox('akID[]', $ak->getAttributeKeyID(), in_array($ak->getAttributeKeyID(), $selectedAKIDs), array('style' => 'vertical-align: middle'))?> <?=$ak->getAttributeKeyDisplayHandle()?></div>
	
<? } ?>

<br/><br/>
<?
$h = Loader::helper('concrete/interface');
$b1 = $h->button_js(t('Save'), 'ccm_submitCustomizeSearchColumnsForm()', 'left');
print $b1;
?>

</form>

<script type="text/javascript">
ccm_submitCustomizeSearchColumnsForm = function() {
	ccm_deactivateSearchResults();
	$("#ccm-page-customize-search-columns-form").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		$("#ccm-page-advanced-search").ajaxSubmit(function(resp) {
			ccm_parseAdvancedSearchResponse(resp);
		});
	});
}

$(function() {
	$('#ccm-page-customize-search-columns-form').submit(function() {
		ccm_submitCustomizeSearchColumnsForm();
	});
});


</script>