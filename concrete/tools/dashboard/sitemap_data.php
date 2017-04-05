<?php

defined('C5_EXECUTE') or die("Access Denied.");

$dh = Core::make('helper/concrete/dashboard/sitemap');
if (!$dh->canRead()) {
    die(t("Access Denied."));
}

if (isset($_REQUEST['displaySingleLevel']) && $_REQUEST['displaySingleLevel']) {

    $provider = \Core::make('Concrete\Core\Application\UserInterface\Sitemap\FlatSitemapProvider');

} else {

    $provider = \Core::make('Concrete\Core\Application\UserInterface\Sitemap\StandardSitemapProvider');
}

$formatter = new \Concrete\Core\Application\UserInterface\Sitemap\JsonFormatter($provider);
print json_encode($formatter);

