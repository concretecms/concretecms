<? defined('C5_EXECUTE') or die("Access Denied.");
if ($_GET['_ccm_dashboard_external']) {
        return;
}
$html = Loader::helper('html');
?><!DOCTYPE html>
<head>
    <link rel="stylesheet" type="text/css" href="<?=$this->getThemePath()?>/main.css" />
<?
$v = View::getInstance();
$v->requireAsset('dashboard');
$v->addFooterItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/i18n_js"></script>'); 
$v->addFooterItem('<script type="text/javascript">$(function() { ConcreteToolbar.start(); });</script>');
if (ENABLE_PROGRESSIVE_PAGE_REINDEX && Config::get('DO_PAGE_REINDEX_CHECK')) {
	$v->addFooterItem('<script type="text/javascript">$(function() { ccm_doPageReindexing(); });</script>');
}
if (Localization::activeLanguage() != 'en') {
	$v->addFooterItem($html->javascript('i18n/ui.datepicker-'.Localization::activeLanguage().'.js'));
}

$valt = Loader::helper('validation/token');
//require(DIR_FILES_ELEMENTS_CORE . '/header_required.php'); 
$v->addHeaderItem($disp);
Loader::element('header_required', array('disableTrackingCode' => true));
$v->addFooterItem('<script type="text/javascript">$(function() { ConcreteDashboard.start(); });</script>');

$u = new User();
$frontendPageID = $u->getPreviousFrontendPageID();
if (!$frontendPageID) {
    $backLink = DIR_REL . '/';
} else {
    $backLink = DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $frontendPageID;
}



?>

</head>
<body>

<div id="ccm-dashboard-page" class="ccm-ui">
<div id="ccm-toolbar">
    <ul>
        <li class="ccm-logo pull-left"><span><?=Loader::helper('concrete/ui')->getToolbarLogoSRC()?></span></li>
        <li class="ccm-toolbar-account pull-left"><a href="<?=$backLink?>"><i class="glyphicon glyphicon-arrow-left"></i></a>
		<li class="pull-right"><a href="<?=URL::to('/dashboard')?>" data-launch-panel="dashboard" class="ccm-launch-panel-active" data-panel-url="<?=URL::to('/system/panels/dashboard')?>"><i class="glyphicon glyphicon-th-large"></i></a>
		<li class="pull-right"><a href="#" data-panel-url="<?=URL::to('/system/panels/sitemap')?>" data-launch-panel="sitemap"><i class="glyphicon glyphicon-list-alt"></i></a></li>
        <li class="ccm-toolbar-search pull-right"><i class="glyphicon glyphicon-search"></i> <input type="search" id="ccm-nav-intelligent-search" tabindex="1" /></li>
    </ul>
</div>
<?
$dh = Loader::helper('concrete/dashboard');
print $dh->getIntelligentSearchMenu();
?>
<div id="ccm-panel-dashboard" class="ccm-panel ccm-panel-right ccm-panel-transition-slide ccm-panel-active ccm-panel-loaded">
	<div class="ccm-panel-content-wrapper ccm-ui">
		<div class="ccm-panel-content ccm-panel-content-visible">
<?
$cnt = new \Concrete\Controller\Panel\Dashboard();
$cnt->setPageObject($c);
$cnt->view();
$nav = $cnt->get('nav');
$tab = $cnt->get('tab');
$ui = $cnt->get('ui');
Loader::element('panels/dashboard', array(
	'nav' => $nav,
	'tab' => $tab,
	'ui' => $ui,
	'c' => $c
)); ?>
</div></div><div class="ccm-panel-shadow-layer"></div>
</div>

<div id="ccm-dashboard-content">
