<? 
Loader::block('library_file');
$ImageExts = array("jpg", "jpeg", "png", "gif");

if(preg_match("/ desc/", $_REQUEST['sort'])) {
	$_REQUEST[sort] = str_replace(" desc", "", $_REQUEST[sort]);
	$_REQUEST[order] = "desc";
}

Loader::library('search');
Loader::model('search/file');

$searchArray = $_REQUEST;
if(!isset($_REQUEST['sort'])) $_REQUEST['sort'] = 'bDateAdded desc';
$s = new FileSearch($searchArray);
$res = $s->getResult($_REQUEST['sort'], $_REQUEST['start'], $_REQUEST['order'], $_REQUEST['view']);

$pOptions = $s->paging($_REQUEST['start'], $_REQUEST['order'], $_REQUEST['view']);


?>

<h2>View Files </h2>
<div class="ccm-al-actions">
<? if ($pOptions['needPaging']) {
	include(DIR_FILES_ELEMENTS_CORE . '/search_results_paging.php');
}?>
</div>

<? include(DIR_FILES_ELEMENTS_CORE . '/search_results_top.php'); ?>

<div class="ccm-al-gallery">
<div class="ccm-spacer">&nbsp;</div>

<?
if ($s->getTotal() > 0) { ?>
	<div class="ccm-al-gallery">
	<div class="ccm-spacer">&nbsp;</div>
	<?

	while ($row = $res->fetchRow()) {

		$size = @getimagesize(DIR_FILES_UPLOADED . '/' . $row['filename']);
		$w = 0;
		$h = 0;
		if ($size) {
			$w = $size[0];
			$h = $size[1];
		}
		$thumbPath = LibraryFileBlockController::getThumbnailAbsolutePath($row['filename']);
		if (file_exists($thumbPath)) {
			$thumbPath = LibraryFileBlockController::getThumbnailRelativePath($row['filename']);
		} else {
			$thumbPath = '';
		}
		?>


		<div class="ccm-al-image" id="ccm-ali<?=$row['bID']?>" al-origfilename="<?=$row['origfilename']?>" al-filename="<?=$row['filename']?>" al-width="<?=$w?>" al-height="<?=$h?>" al-type="<?=$row['type']?>" al-filepath="<?=BASE_URL . REL_DIR_FILES_UPLOADED . '/' . $row['filename']?>" al-thumb-path="<?=$thumbPath?>">
		<div class="ccm-al-inner"><? if ($thumbPath != '') { ?>
		<img src="<?=$thumbPath?>" />
		<? } else { 
			$img = ASSETS_URL_IMAGES . '/icons/filetypes/generic_' . $row['generictype'] . '.png';
		?>
			<?=LibraryFileBlockController::getIcon($row['type'], $row['generictype']); ?>		
		<? } ?>
		</div>
		<div class="ccm-al-title"><?=LibraryFileBlockController::sanitizeTitle($row['filename'], 12)?></div>

		</div>

	<? } ?>

	<div class="ccm-spacer">&nbsp;</div>
	</div>
	<?
} else {
	echo('<strong>No files found.</strong><br/><br/>');
}
?>



<script type="text/javascript">
var ccm_al_img_dblClkTmr = null;
var ccm_al_img_clkObj = null;
if($("div.ccm-al-image")) {
	$("div.ccm-al-image").click( function(e) {
		if (ccm_alSelectedItem != false) {
			ccm_alDeselectItem(ccm_alSelectedItem);
		}
		ccm_al_img_clkObj = this;
		if( ccm_al_img_dblClkTmr ){
			clearTimeout( ccm_al_img_dblClkTmr );
			ccm_al_img_dblClkTmr=null; 
			ccm_priSelectAssetAuto(this.id);
		}else{
			ccm_al_img_dblClkTmr = setTimeout( function() {
				ccm_al_img_dblClkTmr=null;
				ccm_alSelectItem(ccm_al_img_clkObj, e);
			},350);
		}
	});
}
/*
$("div.ccm-al-image").click(function(e) {
	if (ccm_alSelectedItem != false) {
		ccm_alDeselectItem(ccm_alSelectedItem);
	}
	ccm_alSelectItem(this, e);
});
*/
</script>