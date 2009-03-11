<? 
defined('C5_EXECUTE') or die(_("Access Denied."));
global $c;
global $cp;

if (is_object($c)) {
	$pageTitle = (!$pageTitle) ? $c->getCollectionName() : $pageTitle;
	$pageDescription = (!$pageDescription) ? $c->getCollectionDescription() : $pageDescription;
	$cID = $c->getCollectionID(); 
	$isEditMode = ($c->isEditMode()) ? "true" : "false";
	$isArrangeMode = ($c->isArrangeMode()) ? "true" : "false";
	
} else {
	$cID = 1;
}
?>

<meta http-equiv="content-type" content="text/html; charset=<?=APP_CHARSET?>" />

<?
$akt = $c->getCollectionAttributeValue('meta_title'); 
$akd = $c->getCollectionAttributeValue('meta_description');
$akk = $c->getCollectionAttributeValue('meta_keywords');

if ($akt) { 
	$pageTitle = $akt; ?>
	<title><?=$akt?></title>
<? } else { 
	$pageTitle = $c->getCollectionName();
	?>
	<title><?=SITE . ' :: ' . $pageTitle?></title>
<? } 

if ($akd) { ?>
	<meta name="description" content="<?=htmlspecialchars($akd)?>" />
<? } else { ?>	
	<meta name="description" content="<?=htmlspecialchars($pageDescription)?>" />
<? }

if ($akk) { ?>
	<meta name="keywords" content="<?=htmlspecialchars($akk)?>" />
<? } ?>

<? $u = new User(); ?>
<script type="text/javascript">
<?
	if ($u->config('UI_BREADCRUMB')) { 
		echo("var CCM_ENABLE_BREADCRUMB = true;\r");
	} else {
		echo("var CCM_ENABLE_BREADCRUMB = false;\r");
	}
	echo("var CCM_DISPATCHER_FILENAME = '" . DIR_REL . '/' . DISPATCHER_FILENAME . "';\r");
	echo("var CCM_CID = {$cID};\r");
	if (MENU_FEEDBACK_DISPLAY) {
		echo("var CCM_FEEDBACK = true;\r");
	} else {
		echo("var CCM_FEEDBACK = false;\r");
	}
	if (isset($isEditMode)) {
		echo("var CCM_EDIT_MODE = {$isEditMode};\r");
	}
	if (isset($isEditMode)) {
		echo("var CCM_ARRANGE_MODE = {$isArrangeMode};\r");
	}
?>
var CCM_IMAGE_PATH = "<?=ASSETS_URL_IMAGES?>";
var CCM_TOOLS_PATH = "<?=REL_DIR_FILES_TOOLS_REQUIRED?>";
var CCM_REL = "<?=DIR_REL?>";

</script>

<?
$html = Loader::helper('html');
$this->addHeaderItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/i18n_js"></script>'); 

$this->addHeaderItem($html->javascript('jquery.js'));
$this->addHeaderItem($html->javascript('swfobject.js'));
$this->addHeaderItem($html->javascript('ccm.base.js'));
$this->addHeaderItem($html->css('ccm.base.css'));

/* 
//Login Popup Requirements 
$this->addHeaderItem($html->javascript('ccm.popup_login.js'));
$this->addHeaderItem($html->javascript('jquery.form.js'));
$this->addHeaderItem($html->javascript('ccm.dialog.js'));
$this->addHeaderItem($html->css('ccm.dialog.css'));
$this->addHeaderItem($html->css('ccm.popup_login.css'));
*/ 

$favIconFID=intval(Config::get('FAVICON_FID'));
if($favIconFID && file_exists(DIR_BASE.'/favicon.ico')){
	Loader::block('library_file');
	$fileBlock=LibraryFileBlockController::getFile( $favIconFID ); ?> 
	<link rel="shortcut icon" href="<?=$fileBlock->getFileFullURL()?>" type="image/x-icon" />
	<link rel="icon" href="<?=$fileBlock->getFileFullURL()?>" type="image/x-icon" />
<? } ?>

<? 
if (is_object($cp)) {

	if ($this->editingEnabled()) {
		Loader::element('page_controls_header', array('cp' => $cp, 'c' => $c));
	}
	
	if ($this->areLinksDisabled()) { 
		$this->addHeaderItem('<script type="text/javascript">$(function() { ccm_disableLinks(); })</script>');
	}

}

// Finally, we output all header CSS and JavaScript
print $this->controller->outputHeaderItems();

if (is_object($cp)) {

	if ($this->editingEnabled()) {
		Loader::element('page_controls_menu', array('cp' => $cp, 'c' => $c));
	}
}