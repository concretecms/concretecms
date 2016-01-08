<?php
defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();
$dh = Loader::helper('concrete/dashboard');

if (isset($cp)) {
	if ($cp->canViewToolbar()) {

?>

<style type="text/css">div.ccm-page {padding-top: 49px !important;} </style>

<script type="text/javascript">
<?php
$valt = Loader::helper('validation/token');
print "var CCM_SECURITY_TOKEN = '" . $valt->generate() . "';";
?>
</script>

<?php
$dh = Loader::helper('concrete/dashboard');
$v = View::getInstance();
$request = \Request::getInstance();

if (!$dh->inDashboard()) {

	$v->requireAsset('core/app');

	$editMode = $c->isEditMode();
	$tools = REL_DIR_FILES_TOOLS_REQUIRED;
	if ($c->isEditMode()) {
		$startEditMode = 'new Concrete.EditMode();';
	} else {
        $startEditMode = '';
    }

    $launchPageComposer = '';
	if ($cp->canEditPageContents() && $request->get('ctask') == 'check-out-first') {
		$pagetype = $c->getPageTypeObject();
		if (is_object($pagetype) && $pagetype->doesPageTypeLaunchInComposer()) {
			$launchPageComposer = "$('a[data-launch-panel=page]').toggleClass('ccm-launch-panel-active'); ConcretePanelManager.getByIdentifier('page').show();";
		}
	}
	$panelDashboard = URL::to('/ccm/system/panels/dashboard');
	$panelPage = URL::to('/ccm/system/panels/page');
	$panelSitemap = URL::to('/ccm/system/panels/sitemap');
	$panelAdd = URL::to('/ccm/system/panels/add');
	$panelCheckIn = URL::to('/ccm/system/panels/page/check_in');
    $panelMultilingual = URL::to('/ccm/system/panels/multilingual');

	$js = <<<EOL
<script type="text/javascript">$(function() {
	$('html').addClass('ccm-toolbar-visible');
	ConcretePanelManager.register({'identifier': 'dashboard', 'position': 'right', url: '{$panelDashboard}'});
	ConcretePanelManager.register({'identifier': 'page', url: '{$panelPage}'});
	ConcretePanelManager.register({'identifier': 'sitemap', 'position': 'right', url: '{$panelSitemap}'});
	ConcretePanelManager.register({'identifier': 'multilingual', 'position': 'right', url: '{$panelMultilingual}'});
	ConcretePanelManager.register({'identifier': 'add-block', 'translucent': false, 'position': 'left', url: '{$panelAdd}', pinable: true});
	ConcretePanelManager.register({'identifier': 'check-in', 'position': 'left', url: '{$panelCheckIn}'});
	ConcreteToolbar.start();
	{$startEditMode}
	{$launchPageComposer}
});
</script>

EOL;

	$v->addFooterItem($js);

	if (Config::get('concrete.misc.enable_progressive_page_reindex') && Config::get('concrete.misc.do_page_reindex_check')) {
		$v->addFooterItem('<script type="text/javascript">$(function() { ccm_doPageReindexing(); });</script>');
	}
	$cih = Loader::helper('concrete/ui');
	if (Localization::activeLanguage() != 'en') {
		$v->addFooterItem('<script type="text/javascript">$(function() { jQuery.datepicker.setDefaults({dateFormat: \'yy-mm-dd\'}); });</script>');
	}
}

	}

}
