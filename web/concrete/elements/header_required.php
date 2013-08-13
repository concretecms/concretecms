<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$c = $this->getCollectionObject();
if (is_object($c)) {
	$cp = new Permissions($c);
}

/** 
 * Handle page title
 */

if (is_object($c)) {
	// We can set a title 3 ways:
	// 1. It comes through programmatically as $pageTitle. If this is the case then we pass it through, no questions asked
	// 2. It comes from meta title
	// 3. It comes from getCollectionName()
	// In the case of 3, we also pass it through page title format.

	if (!isset($pageTitle) || !$pageTitle) {
		// we aren't getting it dynamically.
		$pageTitle = $c->getCollectionAttributeValue('meta_title');
		if (!$pageTitle) {
			$pageTitle = $c->getCollectionName();
			if($c->isAdminArea()) {
				$pageTitle = t($pageTitle);
			}
			$pageTitle = sprintf(PAGE_TITLE_FORMAT, SITE, $pageTitle);
		}
	}
	$pageDescription = (!$pageDescription) ? $c->getCollectionDescription() : $pageDescription;
	$cID = $c->getCollectionID(); 
	$isEditMode = ($c->isEditMode()) ? "true" : "false";
	$isArrangeMode = ($c->isArrangeMode()) ? "true" : "false";
	
} else {
	$cID = 1;
}
?>

<meta http-equiv="content-type" content="text/html; charset=<?php echo APP_CHARSET?>" />
<?php
$akd = $c->getCollectionAttributeValue('meta_description');
$akk = $c->getCollectionAttributeValue('meta_keywords');
?>
<title><?php echo htmlspecialchars($pageTitle, ENT_COMPAT, APP_CHARSET)?></title>
<?
if ($akd) { ?>
<meta name="description" content="<?=htmlspecialchars($akd, ENT_COMPAT, APP_CHARSET)?>" />
<?php } else { ?>
<meta name="description" content="<?=htmlspecialchars($pageDescription, ENT_COMPAT, APP_CHARSET)?>" />
<?php }
if ($akk) { ?>
<meta name="keywords" content="<?=htmlspecialchars($akk, ENT_COMPAT, APP_CHARSET)?>" />
<?php } 
if($c->getCollectionAttributeValue('exclude_search_index')) { ?>
    <meta name="robots" content="noindex" />
<?php } ?>
<?php
if (defined('APP_VERSION_DISPLAY_IN_HEADER') && APP_VERSION_DISPLAY_IN_HEADER) {
    echo '<meta name="generator" content="concrete5 - ' . APP_VERSION . '" />';
}    
else {
    echo '<meta name="generator" content="concrete5" />';
}
?>

<?php $u = new User(); ?>
<script type="text/javascript">
<?php
	echo("var CCM_DISPATCHER_FILENAME = '" . DIR_REL . '/' . DISPATCHER_FILENAME . "';\r");
	echo("var CCM_CID = ".($cID?$cID:0).";\r");
	if (isset($isEditMode)) {
		echo("var CCM_EDIT_MODE = {$isEditMode};\r");
	}
	if (isset($isEditMode)) {
		echo("var CCM_ARRANGE_MODE = {$isArrangeMode};\r");
	}
?>
var CCM_IMAGE_PATH = "<?php echo ASSETS_URL_IMAGES?>";
var CCM_TOOLS_PATH = "<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>";
var CCM_BASE_URL = "<?php echo BASE_URL?>";
var CCM_REL = "<?php echo DIR_REL?>";

</script>

<?php
$html = Loader::helper('html');
$this->addHeaderItem($html->css('ccm.base.css'), 'CORE');
$this->addHeaderItem($html->javascript('jquery.js'), 'CORE');
$this->addHeaderItem($html->javascript('ccm.base.js', false, true), 'CORE');

$favIconFID=intval(Config::get('FAVICON_FID'));
$appleIconFID =intval(Config::get('IPHONE_HOME_SCREEN_THUMBNAIL_FID'));
$modernIconFID = intval(Config::get('MODERN_TILE_THUMBNAIL_FID'));
$modernIconBGColor = strval(Config::get('MODERN_TILE_THUMBNAIL_BGCOLOR'));

if($favIconFID) {
	$f = File::getByID($favIconFID); ?>
	<link rel="shortcut icon" href="<?php echo $f->getRelativePath()?>" type="image/x-icon" />
	<link rel="icon" href="<?php echo $f->getRelativePath()?>" type="image/x-icon" />
<?php } 

if($appleIconFID) {
	$f = File::getByID($appleIconFID); ?>
	<link rel="apple-touch-icon" href="<?php echo $f->getRelativePath()?>"  />
<?php } 

if($modernIconFID) {
	$f = File::getByID($modernIconFID);
	?><meta name="msapplication-TileImage" content="<?php echo $f->getRelativePath(); ?>" /><?php
	echo "\n";
	if(strlen($modernIconBGColor)) {
		?><meta name="msapplication-TileColor" content="<?php echo $modernIconBGColor; ?>" /><?php
		echo "\n";
	}
} 

if (is_object($cp)) { 

	if ($this->editingEnabled()) {
		Loader::element('page_controls_header', array('cp' => $cp, 'c' => $c));
	}

	if ($this->areLinksDisabled()) { 
		$this->addHeaderItem('<script type="text/javascript">window.onload = function() {ccm_disableLinks()}</script>', 'CORE');
	}
	$cih = Loader::helper('concrete/interface');
	if ($cih->showNewsflowOverlay()) {
		$this->addFooterItem('<script type="text/javascript">$(function() { ccm_showDashboardNewsflowWelcome(); });</script>');
	}	

}

print $this->controller->outputHeaderItems();
$_trackingCodePosition = Config::get('SITE_TRACKING_CODE_POSITION');
if (empty($disableTrackingCode) && $_trackingCodePosition === 'top') {
	echo Config::get('SITE_TRACKING_CODE');
}
echo $c->getCollectionAttributeValue('header_extra_content');
