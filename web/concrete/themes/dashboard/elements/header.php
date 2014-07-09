<? defined('C5_EXECUTE') or die("Access Denied.");
if ($_GET['_ccm_dashboard_external']) {
        return;
}
$html = Loader::helper('html');
?><!DOCTYPE html>
<html <? if (!$hideDashboardPanel) { ?>class="ccm-panel-open ccm-panel-right"<? } ?>>
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
    <div class="ccm-mobile-menu-overlay ccm-mobile-menu-overlay-dashboard hidden-md hidden-lg hidden-sm">
        <div class="ccm-mobile-menu-main">
            <ul class="ccm-mobile-menu-entries">
                <li><i class="fa fa-th-large mobile-leading-icon"></i><a href="<?=URL::to('/dashboard')?>"><?php echo t('Dashboard') ?><i class="fa fa-caret-down"></i></a>
                    <ul class="list-unstyled">
                        <li class="last-li"><a href="<?=View::url('/dashboard/sitemap') ?>"><?php echo t('Sitemap'); ?></a></li>
                        <li class="last-li"><a href="<?=View::url('/dashboard/files') ?>"><?php echo t('Files'); ?></a></li>
                        <li class="last-li"><a href="<?=View::url('/dashboard/users') ?>"><?php echo t('Members'); ?></a></li>
                        <li class="last-li"><a href="<?=View::url('/dashboard/reports') ?>"><?php echo t('Reports'); ?></a></li>
                        <li class="last-li"><a href="<?=View::url('/dashboard/pages') ?>"><?php echo t('Pages & Themes'); ?></a></li>
                        <li class="last-li"><a href="<?=View::url('/dashboard/workflow') ?>"><?php echo t('Workflow'); ?></a></li>
                        <li class="last-li"><a href="<?=View::url('/dashboard/blocks/stacks') ?>"><?php echo t('Stacks & Blocks'); ?></a></li>
                        <li class="last-li"><a href="<?=View::url('/dashboard/extend') ?>"><?php echo t('Extend concrete5'); ?></a></li>
                        <li class="last-li"><a href="<?=View::url('/dashboard/system') ?>"><?php echo t('System & Settings'); ?></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
<div id="ccm-toolbar">
    <ul>
        <li class="ccm-logo pull-left"><span><?=Loader::helper('concrete/ui')->getToolbarLogoSRC()?></span></li>
        <li class="ccm-toolbar-account pull-left"><a href="<?=$backLink?>"><i class="fa fa-arrow-left"></i></a>
        <li class="pull-right hidden-xs"><a href="<?=URL::to('/dashboard')?>" data-launch-panel="dashboard" <? if (!$hideDashboardPanel) { ?>class="ccm-launch-panel-active" <? } ?> data-panel-url="<?=URL::to('/system/panels/dashboard')?>"><i class="fa fa-th-large"></i></a>
        <li class="pull-right hidden-xs"><a href="#" data-panel-url="<?=URL::to('/system/panels/sitemap')?>" data-launch-panel="sitemap"><i class="fa fa-list-alt"></i></a></li>
        <li class="ccm-toolbar-search pull-right hidden-xs"><i class="fa fa-search"></i> <input type="search" id="ccm-nav-intelligent-search" tabindex="1" /></li>
        <li class="pull-right ccm-toolbar-mobile-menu-button visible-xs hidden-sm hidden-md hidden-lg"><i class="fa fa-bars fa-2"></i></li>
    </ul>
</div>
<?
$dh = Loader::helper('concrete/dashboard');
print $dh->getIntelligentSearchMenu();

if (!$hideDashboardPanel) { ?>

<div id="ccm-panel-dashboard" class="hidden-xs ccm-panel ccm-panel-right ccm-panel-transition-slide ccm-panel-active ccm-panel-loaded">
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

<? } ?>

<div id="ccm-dashboard-content" class="container-fluid">
