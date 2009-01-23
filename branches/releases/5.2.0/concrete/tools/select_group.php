<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
$uc = Page::getByPath("/dashboard/users/groups");
$ucp = new Permissions($uc);
if (!$ucp->canRead()) {
	die(_("You have no access to groups."));
}

if (!$_REQUEST['group_submit_search']) { ?>
<div id="ccm-group-search-wrapper">
<?php  } ?>

<?php  
Loader::model('search/group');
$gl = new GroupSearch();
if (isset($_GET['gKeywords'])) {
	$gl->filterByKeywords($_GET['gKeywords']);
}

$gResults = $gl->getPage();

?>

<form id="ccm-group-search" method="get" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/select_group/">
<div id="ccm-group-search-fields">
<input type="text" id="ccm-group-search-keywords" name="gKeywords" value="<?php echo $_REQUEST['gKeywords']?>" class="ccm-text" style="width: 100px" />
<input type="submit" value="<?php echo t('Search')?>" />
<input type="hidden" name="group_submit_search" value="1" />
</div>
</form>

<?php  if (count($gResults) > 0) { 

	$gl->displaySummary();

	foreach ($gResults as $g) { ?>

	<div class="ccm-group">
		<a class="ccm-group-inner" id="g<?php echo $g['gID']?>" group-id="<?php echo $g['gID']?>" group-name="<?php echo $g['gName']?>" href="javascript:void(0)" style="background-image: url(<?php echo ASSETS_URL_IMAGES?>/icons/group.png)"><?php echo $g['gName']?></a>
		<div class="ccm-group-description"><?php echo $g['gDescription']?></div>
	</div>


<?php  } ?>

<div id="ccm-group-paging">
<?php 
$url = REL_DIR_FILES_TOOLS_REQUIRED . '/select_group?gKeywords=' . $_REQUEST['gKeywords'] . '&ccm_paging_p=%pageNum%';
$gl->displayPaging($url);
?>
</div>

<?php 

} else { ?>

	<p><?php echo t('No groups found.')?></p>
	
<?php  } ?>

<?php  if (!$_REQUEST['group_submit_search']) { ?>

</div>


<?php  } ?>

<script type="text/javascript">
$(function() {
	ccm_setupGroupSearch();
});
</script>
