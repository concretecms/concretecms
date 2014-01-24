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
$v = View::getInstance();

if (!$dh->inDashboard()) {

	$v->requireAsset('core/app');
	/*
	Loader::library("3rdparty/mobile_detect");
	$md = new Mobile_Detect();
	if ($md->isMobile()) {
		$this->addHeaderItem($html->css('ccm.app.mobile.css'));
		$this->addFooterItem($html->javascript('jquery.ui.touch-punch.js'));
	}
	*/

	$editMode = $c->isEditMode();
	$tools = REL_DIR_FILES_TOOLS_REQUIRED;
	if ($c->isEditMode()) {
		$startEditMode = 'new Concrete.EditMode();';
	}
	if ($cp->canEditPageContents() && $_REQUEST['ctask'] == 'check-out-first') {
		$pagetype = $c->getPageTypeObject();
		if (is_object($pagetype) && $pagetype->doesPageTypeLaunchInComposer()) {
			$launchPageComposer = "$('a[data-launch-panel=page]').toggleClass('ccm-launch-panel-active'); CCMPanelManager.getByIdentifier('page').show();";
		}
	}
	$panelDashboard = URL::to('/system/panels/dashboard');
	$panelPage = URL::to('/system/panels/page');
	$panelSitemap = URL::to('/system/panels/sitemap');
	$panelAdd = URL::to('/system/panels/add');
	$panelCheckIn = URL::to('/system/panels/page/check_in');

	$js = <<<EOL
<script type="text/javascript" src="{$tools}/i18n_js"></script>
<script type="text/javascript">$(function() {
	$('html').addClass('ccm-toolbar-visible');
	CCMPanelManager.register({'identifier': 'dashboard', 'position': 'right', url: '{$panelDashboard}'});
	CCMPanelManager.register({'identifier': 'page', url: '{$panelPage}'});
	CCMPanelManager.register({'identifier': 'sitemap', 'position': 'right', url: '{$panelSitemap}'});
	CCMPanelManager.register({'identifier': 'add-block', 'translucent': false, 'position': 'left', url: '{$panelAdd}'});
	CCMPanelManager.register({'identifier': 'check-in', 'position': 'left', url: '{$panelCheckIn}'});
	CCMToolbar.start();
	{$startEditMode}
	{$launchPageComposer}
});
</script>

EOL;

	$this->addFooterItem($js);

	if (ENABLE_PROGRESSIVE_PAGE_REINDEX && Config::get('DO_PAGE_REINDEX_CHECK')) {
		$this->addFooterItem('<script type="text/javascript">$(function() { ccm_doPageReindexing(); });</script>');
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