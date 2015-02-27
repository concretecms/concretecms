<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<div style="height: 100%">
<?php foreach($_REQUEST['cvID'] as $cvID) {
	$tabs[] = array('view-version-' . $cvID, t('Version %s', $cvID), $checked);
	$checked = false;
} 
print $ih->tabs($tabs);			

foreach($_REQUEST['cvID'] as $cvID) { ?>

	<div id="ccm-tab-content-view-version-<?=$cvID?>" style="display: <?=$display?>; height: 100%">
	<iframe border="0" id="v<?=time()?>" frameborder="0" height="100%" width="100%" src="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/preview_version?cvID=<?=$cvID?>&amp;cID=<?=$_REQUEST['cID']?>" />
	</div>
	
	<?php if ($display == 'block') {
		$display = 'none';
	} ?>

<?php } ?>
</div>