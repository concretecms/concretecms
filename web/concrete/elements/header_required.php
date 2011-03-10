<? 
defined('C5_EXECUTE') or die("Access Denied.");
global $c;
global $cp;
global $cvID;

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
	$pageTitle = $akt; 
	?><title><?=htmlspecialchars($akt, ENT_COMPAT, APP_CHARSET)?></title>
<? } else { 
	$pageTitle = htmlspecialchars($c->getCollectionName(), ENT_COMPAT, APP_CHARSET);
	?><title><?=sprintf(PAGE_TITLE_FORMAT, SITE, $pageTitle)?></title>
<? } 

if ($akd) { 
?><meta name="description" content="<?=htmlspecialchars($akd, ENT_COMPAT, APP_CHARSET)?>" />
<? } else { 
?><meta name="description" content="<?=htmlspecialchars($pageDescription, ENT_COMPAT, APP_CHARSET)?>" />
<? }
if ($akk) { ?><meta name="keywords" content="<?=htmlspecialchars($akk, ENT_COMPAT, APP_CHARSET)?>" />
<? } ?>
<meta name="generator" content="concrete5 - <?=APP_VERSION ?>" />

<? $u = new User(); ?>
<script type="text/javascript">
<?
	echo("var CCM_DISPATCHER_FILENAME = '" . DIR_REL . '/' . DISPATCHER_FILENAME . "';\r");
	echo("var CCM_CID = ".($cID?$cID:0).";\r");
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
$this->addHeaderItem($html->css('ccm.base.css'), 'CORE');
$this->addHeaderItem($html->javascript('jquery.js'), 'CORE');
$this->addHeaderItem($html->javascript('ccm.base.js'), 'CORE');

$favIconFID=intval(Config::get('FAVICON_FID'));


if($favIconFID) {
	$f = File::getByID($favIconFID); ?>
	<link rel="shortcut icon" href="<?=$f->getRelativePath()?>" type="image/x-icon" />
	<link rel="icon" href="<?=$f->getRelativePath()?>" type="image/x-icon" />
<? } ?>

<?  
if (is_object($cp)) { 

	if ($this->editingEnabled()) {
		Loader::element('page_controls_header', array('cp' => $cp, 'c' => $c));
	}

	if ($this->areLinksDisabled()) { 
		$this->addHeaderItem('<script type="text/javascript">window.onload = function() {ccm_disableLinks()}</script>', 'CORE');
	}

}

print $this->controller->outputHeaderItems();
echo $c->getCollectionAttributeValue('header_extra_content');