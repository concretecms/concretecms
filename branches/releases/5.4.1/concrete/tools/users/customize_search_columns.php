<?php  defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('user_attributes');
$form = Loader::helper('form');
$tp = new TaskPermission();
if (!$tp->canAccessUserSearch()) { 
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

<form method="post" id="ccm-user-customize-search-columns-form" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/users/customize_search_columns/">
<?php echo $form->hidden('task', 'update_columns')?>

<h1><?php echo t('Additional Searchable Attributes')?></h1>

<p><?php echo t('Choose the additional attributes you wish to include as column headers.')?></p>

<?php  foreach($list as $ak) { ?>

	<div><?php echo $form->checkbox('akID[]', $ak->getAttributeKeyID(), in_array($ak->getAttributeKeyID(), $selectedAKIDs), array('style' => 'vertical-align: middle'))?> <?php echo $ak->getAttributeKeyDisplayHandle()?></div>
	
<?php  } ?>

<br/><br/>
<?php 
$h = Loader::helper('concrete/interface');
$b1 = $h->submit(t('Save'), 'save', 'left');
print $b1;
?>

</form>

<script type="text/javascript">
ccm_submitCustomizeSearchColumnsForm = function() {
	ccm_deactivateSearchResults();
	$("#ccm-user-customize-search-columns-form").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		$("#ccm-user-advanced-search").ajaxSubmit(function(resp) {
			ccm_parseAdvancedSearchResponse(resp, 'user');
		});
	});
	return false;
}

$(function() {
	$('#ccm-user-customize-search-columns-form').submit(function() {
		return ccm_submitCustomizeSearchColumnsForm();
	});
});


</script>