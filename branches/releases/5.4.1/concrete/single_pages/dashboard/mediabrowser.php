<?php 
defined('C5_EXECUTE') or die("Access Denied.");
Loader::library('search');
Loader::model('search/file');
Loader::model('collection_types');
Loader::block('image');

$cID = $c->getCollectionID();
$ImageExts = array("jpg", "jpeg", "png", "gif");

if ($_GET['task'] == 'delete') {
	$b = Block::getByID($_GET['bID']);
	if (is_object($b)) {
		$orgCID = $b->getOriginalCollection();
		$b->deleteBlock(true);
		if($orgCID == $cID) {
			$c->approvePendingAction();
		}
		$message = t("File Deleted.");
	}
}

if($_REQUEST[uploadNewFile]) {
	// lets try to determine if this is an image file!
	$names = explode(".", $_FILES['filename']['name']);
	$name_count = count($names)-1;
	$fext = strtolower($names[$name_count]);
	foreach($ImageExts as $imgExt) {
		if($fext == $imgExt) { $fileType = "image"; }
	}
	##### end figuring out what kind of file this is

	// lets just stick this all in a hidden area
	$cp = new Permissions($c);

	if($fileType == "image") {
		// add an image block!
		$bt = BlockType::getByHandle('image');
		$nb = $bt->addBlock($c, '_al:image');
		if (is_object($nb)) {
			$bci = new BlockContentImage($nb);
			$row = $bci->getContent();
			$filePath = REL_DIR_FILES_UPLOADED . '/' . $row['filename'];
			$newFileBID = $bci->bID;
		}
	} else {
		// add a regular file block!
		$bt = BlockType::getByHandle('file');
		$_POST['fileType'] = 'file';
		$nb = $bt->addBlock($c, '_al:file');
		if (is_object($nb)) {
			$bci = new BlockContentFile($nb);
			$fRow = $bci->getContent();
			$filePath = $fRow['path'];
			$newFileBID = $bci->bID;
		}
	}

	// new file is uploaded! we should have this file selected
	$_GET['sort'] = "bDateAdded desc";
	$selectThis = $newFileBID;
}


if(preg_match("/ desc/", $_GET['sort'])) {
	$_GET[sort] = str_replace(" desc", "", $_GET['sort']);
	$_GET[order] = "desc";
}
$searchArray = $_GET;
$s = new FileSearch($searchArray);

?>

<h1><span><?php echo t('Images and Files')?></span></h1>

<div class="ccm-dashboard-inner">

<?php  include(DIR_FILES_TOOLS_REQUIRED . '/al.php'); ?>

</div>