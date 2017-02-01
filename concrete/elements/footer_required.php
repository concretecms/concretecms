<?php
use Concrete\Core\Support\Facade\Application;

defined('C5_EXECUTE') or die('Access Denied.');

// Arguments
/* @var bool $disableTrackingCode: set to true to avoid including the footer tracking code. */
/* @var bool $display_account_menu: set to true to display the user menu, false to avoid it, null (or not set) to use the default confguration option. */

$app = Application::getFacadeApplication();
$c = Page::getCurrentPage();
$site = $app->make('site')->getSite();
$config = $site->getConfigRepository();

if (is_object($c)) {
    $cp = new Permissions($c);
    View::element('page_controls_footer', ['cp' => $cp, 'c' => $c]);
}

if (empty($disableTrackingCode)) {
    echo $config->get('seo.tracking.code.footer');
}

View::getInstance()->markFooterAssetPosition();

if (!isset($display_account_menu)) {
    $display_account_menu = $config->get('user.display_account_menu');
}
if ($display_account_menu) {
    $dh = $app->make('helper/concrete/dashboard');
    if (!$dh->inDashboard($c)) {
        View::element('account/menu');
    }
}
