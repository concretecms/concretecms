<?
defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();
$html = Loader::helper('html');
$dh = Loader::helper('concrete/dashboard');

if (isset($cp)) {
	if ($cp->canViewToolbar()) { 

?>

<style type="text/css">body {margin-top: 49px !important;} </style>

<script type="text/javascript">
<?
$valt = Loader::helper('validation/token');
print "var CCM_SECURITY_TOKEN = '" . $valt->generate() . "';";
?>
</script>

<?
$dh = Loader::helper('concrete/dashboard');
if (!$dh->inDashboard()) {
	$this->addHeaderItem($html->css('ccm.app.css'));
	if (MOBILE_THEME_IS_ACTIVE == true) {
		$this->addHeaderItem($html->css('ccm.app.mobile.css'));
	}
	$this->addHeaderItem($html->css('jquery.ui.css'));
	$this->addFooterItem('<div id="ccm-page-controls-wrapper"><div id="ccm-toolbar"></div></div>');
	
	$this->addFooterItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/i18n_js"></script>'); 
	$this->addHeaderItem($html->javascript('jquery.js'));
	$this->addFooterItem($html->javascript('jquery.ui.js'));
	$this->addFooterItem($html->javascript('jquery.form.js'));
	$this->addFooterItem($html->javascript('jquery.rating.js'));
	$this->addFooterItem($html->javascript('bootstrap.js'));
	$this->addFooterItem($html->javascript('ccm.app.js'));
	if (ENABLE_PROGRESSIVE_PAGE_REINDEX && Config::get('DO_PAGE_REINDEX_CHECK')) {
		$this->addHeaderItem('<script type="text/javascript">$(function() { ccm_doPageReindexing(); });</script>');
	}
	$cih = Loader::helper('concrete/interface');
	if (LANGUAGE != 'en') {
		$this->addFooterItem($html->javascript('i18n/ui.datepicker-' . LANGUAGE . '.js'));
		$this->addFooterItem('<script type="text/javascript">$(function() { jQuery.datepicker.setDefaults({dateFormat: \'yy-mm-dd\'}); });</script>');
	}
	if (!Config::get('SEEN_INTRODUCTION')) {
		$this->addHeaderItem('<script type="text/javascript">$(function() { ccm_showAppIntroduction(); });</script>');
		Config::save('SEEN_INTRODUCTION', 1);
	}
	$this->addFooterItem($html->javascript('tiny_mce/tiny_mce.js'));
}

$cID = ($c->isAlias()) ? $c->getCollectionPointerOriginalID() : $c->getCollectionID();
$btask = '';
if (Loader::helper('validation/strings')->alphanum($_REQUEST['btask'])) {
	$btask = $_REQUEST['btask'];
}
$this->addFooterItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/page_controls_menu_js?cID=' . $cID . '&amp;cvID=' . $cvID . '&amp;btask=' . $btask . '&amp;ts=' . time() . '"></script>'); 

	}
	
}