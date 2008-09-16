<? if (!$_REQUEST['group_submit_search']) { ?>
<div id="ccm-group-search-wrapper">
<? } ?>

<? 
Loader::model('search/group');
$gl = new GroupSearch($_GET);
if ($gl->getTotal() > 0) {
	$gResults = $gl->getResult($_GET['sort'], $_GET['start'], $_GET['order'], 40);
	$pOptions = $gl->paging($_GET['start'], $_GET['order'], 10);
}

?>

<form id="ccm-group-search" method="get" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/select_group/">
<div id="ccm-group-search-fields">
<input type="text" id="ccm-group-search-keywords" name="gKeywords" value="<?=$_REQUEST['gKeywords']?>" class="ccm-text" style="width: 100px" />
<input type="submit" value="Search" />
<input type="hidden" name="group_submit_search" value="1" />
</div>
</form>

<? if ($gl->getTotal() > 0) { ?>

<? foreach ($gResults as $g) { ?>

	<div class="ccm-group">
		<a class="ccm-group-inner" id="g<?=$g['gID']?>" group-id="<?=$g['gID']?>" group-name="<?=$g['gName']?>" href="javascript:void(0)" style="background-image: url(<?=ASSETS_URL_IMAGES?>/icons/group.png)"><?=$g['gName']?></a>
		<div class="ccm-group-description"><?=$g['gDescription']?></div>
	</div>


<? }

if ($pOptions['needPaging']) { 
	$pOptions['script'] = REL_DIR_FILES_TOOLS_REQUIRED . '/select_group';	?>
	<div id="ccm-group-paging">
	<? include(DIR_FILES_ELEMENTS_CORE . '/search_results_paging.php'); ?>
	</div>
<? }


} else { ?>

	<p>No groups found.</p>
	
<? } ?>

<? if (!$_REQUEST['group_submit_search']) { ?>

</div>


<? } ?>

<script type="text/javascript">
$(function() {
	ccm_setupGroupSearch();
});
</script>
