<?
defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();
$html = Loader::helper('html');
$dh = Loader::helper('concrete/dashboard');

if (isset($cp)) {
	if ($cp->canViewToolbar()) { 

?>

<style type="text/css">html {margin-top: 49px !important;} </style>

<script type="text/javascript">
<?
$valt = Loader::helper('validation/token');
print "var CCM_SECURITY_TOKEN = '" . $valt->generate() . "';";
?>
</script>

<?
$dh = Loader::helper('concrete/dashboard');
$req = Request::get();

if (!$dh->inDashboard()) {

	$req->requireAsset('jquery');
	$req->requireAsset('redactor');

	$this->addHeaderItem($html->css('ccm.app.css'));
	$this->addHeaderItem($html->css('jquery.ui.css'));
	
	$this->addFooterItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/i18n_js"></script>'); 
	$this->addFooterItem($html->javascript('jquery.ui.js'));
	Loader::library("3rdparty/mobile_detect");
	$md = new Mobile_Detect();
	if ($md->isMobile()) {
		$this->addHeaderItem($html->css('ccm.app.mobile.css'));
		$this->addFooterItem($html->javascript('jquery.ui.touch-punch.js'));
	}
	$this->addFooterItem($html->javascript('jquery.form.js'));
	$this->addFooterItem($html->javascript('jquery.rating.js'));
	$this->addFooterItem($html->javascript('bootstrap.js'));
	$this->addFooterItem($html->javascript('ccm.app.js'));
	$this->addFooterItem('<script type="text/javascript">$(function() { CCMToolbar.start(); });</script>');
	if ($c->isEditMode()) {
		$this->addFooterItem('<script type="text/javascript">$(function() { CCMEditMode.start(); });</script>');
	}
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
}

	}
	
}