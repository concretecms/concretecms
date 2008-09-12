<?php  if (!$_REQUEST['group_submit_search']) { ?>
<div id="ccm-group-search-wrapper">
<?php  } ?>

<?php  
Loader::model('search/group');
$gl = new GroupSearch($_GET);
if ($gl->getTotal() > 0) {
	$gResults = $gl->getResult($_GET['sort'], $_GET['start'], $_GET['order'], 40);
	$pOptions = $gl->paging($_GET['start'], $_GET['order'], 10);
}

?>

<form id="ccm-group-search" method="get" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/select_group/">
<div id="ccm-group-search-fields">
<input type="text" id="ccm-group-search-keywords" name="gKeywords" value="<?php echo $_REQUEST['gKeywords']?>" class="ccm-text" style="width: 100px" />
<input type="submit" value="Search" />
<input type="hidden" name="group_submit_search" value="1" />
</div>
</form>

<?php  if ($gl->getTotal() > 0) { ?>

<?php  foreach ($gResults as $g) { ?>

	<div class="ccm-group">
		<a class="ccm-group-inner" id="g<?php echo $g['gID']?>" group-id="<?php echo $g['gID']?>" group-name="<?php echo $g['gName']?>" href="javascript:void(0)" style="background-image: url(<?php echo ASSETS_URL_IMAGES?>/icons/group.png)"><?php echo $g['gName']?></a>
		<div class="ccm-group-description"><?php echo $g['gDescription']?></div>
	</div>


<?php  }

if ($pOptions['needPaging']) { 
	$pOptions['script'] = REL_DIR_FILES_TOOLS_REQUIRED . '/select_group';	?>
	<div id="ccm-group-paging">
	<?php  include(DIR_FILES_ELEMENTS_CORE . '/search_results_paging.php'); ?>
	</div>
<?php  }


} else { ?>

	<p>No groups found.</p>
	
<?php  } ?>

<?php  if (!$_REQUEST['group_submit_search']) { ?>

</div>


<?php  } ?>

<script type="text/javascript">
$(function() {
	ccm_setupGroupSearch();
});
</script>
