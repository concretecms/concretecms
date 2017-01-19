<?php
use Concrete\Core\Support\Facade\Application;

defined('C5_EXECUTE') or die('Access Denied.');

// Arguments
/* @var bool $disableTrackingCode: set to true to avoid including the footer tracking code. */
/* @var bool $display_user_menu: set to true to display the user menu, false to avoid it, null (or not set) to use the concrete.accessibility.display_user_menu confguration option. */

$app = Application::getFacadeApplication();
$c = Page::getCurrentPage();
$site = $app->make('site')->getSite();

if (is_object($c)) {
    $cp = new Permissions($c);
    View::element('page_controls_footer', ['cp' => $cp, 'c' => $c]);
}

if (empty($disableTrackingCode)) {
    echo $site->getConfigRepository()->get('seo.tracking.code.footer');
}

View::getInstance()->markFooterAssetPosition();

if (!isset($display_user_menu)) {
    $display_user_menu = $app->make('config')->get('concrete.accessibility.display_user_menu');
}
if ($display_user_menu) {
    $dh = $app->make('helper/concrete/dashboard');
    if (!$dh->inDashboard($c)) {
        View::element('account/menu');
    }
}
