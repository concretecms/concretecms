<?php  

defined('C5_EXECUTE') or die(_("Access Denied."));

$c = Page::getByPath('/dashboard/mediabrowser');
$cp = new Permissions($c);
if (!$cp->canRead()) {
	die(_("Access Denied."));
}

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

<h2><?php echo t('View Files')?> </h2>
<div class="ccm-al-actions">
<?php  if ($pOptions['needPaging']) {
	include(DIR_FILES_ELEMENTS_CORE . '/search_results_paging.php');
}?>
</div>

<?php  include(DIR_FILES_ELEMENTS_CORE . '/search_results_top.php'); ?>

<div class="ccm-al-gallery">
<div class="ccm-spacer">&nbsp;</div>

<?php 
if ($s->getTotal() > 0) { ?>
	<div class="ccm-al-gallery">
	<div class="ccm-spacer">&nbsp;</div>
	<?php 

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


		<div class="ccm-al-image" id="ccm-ali<?php echo $row['bID']?>" al-origfilename="<?php echo $row['origfilename']?>" al-filename="<?php echo $row['filename']?>" al-width="<?php echo $w?>" al-height="<?php echo $h?>" al-type="<?php echo $row['type']?>" al-filepath="<?php echo BASE_URL . REL_DIR_FILES_UPLOADED . '/' . $row['filename']?>" al-thumb-path="<?php echo $thumbPath?>">
		<div class="ccm-al-inner"><?php  if ($thumbPath != '') { ?>
		<img src="<?php echo $thumbPath?>" />
		<?php  } else { 
			$img = ASSETS_URL_IMAGES . '/icons/filetypes/generic_' . $row['generictype'] . '.png';
		?>
			<?php echo LibraryFileBlockController::getIcon($row['type'], $row['generictype']); ?>		
		<?php  } ?>
		</div>
		<div class="ccm-al-title"><?php echo LibraryFileBlockController::sanitizeTitle($row['filename'], 12)?></div>

		</div>

	<?php  } ?>

	<div class="ccm-spacer">&nbsp;</div>
	</div>
	<?php 
} else {
	echo('<strong>' . t('No files found.') . '</strong><br/><br/>');
}
?>



<script type="text/javascript">
var ccm_al_img_dblClkTmr = null;
var ccm_al_img_clkObj = null;
if($("div.ccm-al-image")) {
	// double click behavior is super buggy
	$("div.ccm-al-image").click( function(e) {
		if (ccm_alSelectedItem != false) {
			ccm_alDeselectItem(ccm_alSelectedItem);
		}
		e.stopPropagation();
		ccm_alSelectItem(this, e);
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