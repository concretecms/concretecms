<? defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('user_attributes');
$form = Loader::helper('form');
$c1 = Page::getByPath('/dashboard/users');
$cp1 = new Permissions($c1);
$c2 = Page::getByPath('/dashboard/users/groups');
$cp2 = new Permissions($c2);
if ((!$cp1->canRead()) && (!$cp2->canRead())) {
	die(_("Access Denied."));
}

$selectedAKIDs = array();
$slist = UserAttributeKey::getColumnHeaderList();
foreach($slist as $sk) {
	$selectedAKIDs[] = $sk->getAttributeKeyID();
}

if ($_POST['task'] == 'update_columns') {
	Loader::model('attribute/category');
	$sc = AttributeKeyCategory::getByHandle('user');
	$sc->clearAttributeKeyCategoryColumnHeaders();
	
	if (is_array($_POST['akID'])) {
		foreach($_POST['akID'] as $akID) {
			$ak = UserAttributeKey::getByID($akID);
			$ak->setAttributeKeyColumnHeader(1);
		}
	}
	
	exit;
}

$list = UserAttributeKey::getList();

?>

<form method="post" id="ccm-user-customize-search-columns-form" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/users/customize_search_columns/">
<?=$form->hidden('task', 'update_columns')?>

<h1><?=t('Additional Searchable Attributes')?></h1>

<p><?=t('Choose the additional attributes you wish to include as column headers.')?></p>

<? foreach($list as $ak) { ?>

	<div><?=$form->checkbox('akID[]', $ak->getAttributeKeyID(), in_array($ak->getAttributeKeyID(), $selectedAKIDs), array('style' => 'vertical-align: middle'))?> <?=$ak->getAttributeKeyName()?></div>
	
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
	$("#ccm-user-customize-search-columns-form").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		$("#ccm-user-advanced-search").ajaxSubmit(function(resp) {
			ccm_parseAdvancedSearchResponse(resp);
		});
	});
}

$(function() {
	$('#ccm-file-customize-search-columns-form').submit(function() {
		ccm_submitCustomizeSearchColumnsForm();
	});
});


</script>