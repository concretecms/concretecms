<?php defined('C5_EXECUTE') or die('Access Denied.');

if (Request::getInstance()->get('_ccm_dashboard_external')) {
    return;
}
$html = Core::make('helper/html');
/* @var Concrete\Core\Html\Service\Html $html */

$valt = Core::make('helper/validation/token');
/* @var Concrete\Core\Validation\CSRF\Token $valt */

if (!isset($hideDashboardPanel)) {
    $hideDashboardPanel = false;
}

?><!DOCTYPE html>
<html<?= $hideDashboardPanel ? '' : ' class="ccm-panel-open ccm-panel-right"'; ?>>
<head>
    <link rel="stylesheet" type="text/css" href="<?=$this->getThemePath()?>/main.css" />
<?php
$v = View::getRequestInstance();
$v->requireAsset('dashboard');
$v->requireAsset('javascript-localized', 'core/localization');
$v->addFooterItem('<script type="text/javascript">$(function() { ConcreteToolbar.start(); });</script>');
if (Config::get('concrete.misc.enable_progressive_page_reindex') && Config::get('concrete.misc.do_page_reindex_check')) {
    $v->addFooterItem('<script type="text/javascript">$(function() { ccm_doPageReindexing(); });</script>');
}
if (Localization::activeLanguage() != 'en') {
    $v->addFooterItem($html->javascript('i18n/ui.datepicker-'.Localization::activeLanguage().'.js'));
}

$v->addHeaderItem('<meta name="viewport" content="width=device-width, initial-scale=1">');
View::element('header_required', array('disableTrackingCode' => true));
$v->addFooterItem('<script type="text/javascript">$(function() { ConcreteDashboard.start(); });</script>');

$u = new User();
$frontendPageID = $u->getPreviousFrontendPageID();
if (!$frontendPageID) {
    $backLink = DIR_REL . '/';
} else {
    $backLink = DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $frontendPageID;
}

$show_titles = (bool) Config::get('concrete.accessibility.toolbar_titles');
$large_font = (bool) Config::get('concrete.accessibility.toolbar_large_font');

?>

</head>
<body>

<div id="ccm-dashboard-page" class="ccm-ui">
    <div class="ccm-mobile-menu-overlay ccm-mobile-menu-overlay-dashboard hidden-md hidden-lg">
        <div class="ccm-mobile-menu-main">
            <ul class="ccm-mobile-menu-entries">
                <li><i class="fa fa-sliders mobile-leading-icon"></i><a href="<?=URL::to('/dashboard')?>"><?php echo t('Dashboard') ?><i class="fa fa-caret-down"></i></a>
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
                <li>
                    <i class="fa fa-sign-out mobile-leading-icon"></i><a href="<?= URL::to('/login', 'logout', $valt->generate('logout')); ?>"><?= t('Sign Out'); ?></a>
                </li>
            </ul>
        </div>
    </div>
<div id="ccm-toolbar" class="<?= $show_titles ? 'titles' : '' ?> <?= $large_font ? 'large-font' : '' ?>">
    <ul>
        <li class="ccm-logo pull-left"><span><?=Loader::helper('concrete/ui')->getToolbarLogoSRC()?></span></li>
        <li class="ccm-toolbar-account pull-left">
            <a href="<?=$backLink?>">
                <i class="fa fa-arrow-left"></i>
                <span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-return">
                    <?= tc('toolbar', 'Return to Website') ?>
                </span>
            </a>
        </li>
        <li class="pull-right hidden-xs hidden-sm">
            <a href="<?=URL::to('/dashboard')?>"
                data-launch-panel="dashboard"
                <?= $hideDashboardPanel ? '' : ' class="ccm-launch-panel-active"' ?>
                data-panel-url="<?=URL::to('/system/panels/dashboard')?>">
                <i class="fa fa-sliders"></i>
                <span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-site-settings">
                    <?= tc('toolbar', 'Dashboard') ?>
                </span>
            </a>
        </li>
        <li class="pull-right hidden-xs hidden-sm">
            <a href="#" data-panel-url="<?=URL::to('/system/panels/sitemap')?>" data-launch-panel="sitemap">
                <i class="fa fa-files-o"></i>
                <span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-add-page">
                    <?= tc('toolbar', 'Pages') ?>
                </span>
            </a>
        </li>
        <li class="ccm-toolbar-search pull-right hidden-xs hidden-sm">
            <i class="fa fa-search"></i>
            <input type="search" id="ccm-nav-intelligent-search" tabindex="1" />
        </li>
        <li class="pull-right ccm-toolbar-mobile-menu-button visible-xs visible-sm hidden-md hidden-lg">
            <i class="fa fa-bars"></i>
        </li>
    </ul>
</div>
<?php
$dh = Core::make('helper/concrete/dashboard');
echo $dh->getIntelligentSearchMenu();

if (!$hideDashboardPanel) {
    ?>
    <div id="ccm-panel-dashboard" class="hidden-xs hidden-sm ccm-panel ccm-panel-right ccm-panel-transition-slide ccm-panel-active ccm-panel-loaded">
        <div class="ccm-panel-content-wrapper ccm-ui">
            <div class="ccm-panel-content ccm-panel-content-visible">
                <?php
                $cnt = new \Concrete\Controller\Panel\Dashboard();
    $cnt->setPageObject($c);
    $cnt->view();
    $nav = $cnt->get('nav');
    $tab = $cnt->get('tab');
    $ui = $cnt->get('ui');
    View::element(
                    'panels/dashboard',
                    array(
                        'nav' => $nav,
                        'tab' => $tab,
                        'ui' => $ui,
                        'c' => $c,
                    )
                );
    ?>
            </div>
        </div>
    </div>
    <?php 
}
?>

<div id="ccm-dashboard-content" class="container-fluid">
