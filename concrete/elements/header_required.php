<?php
use Concrete\Core\Support\Facade\Application;

defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();
$cp = false;
$isEditMode = false;
$isArrangeMode = false;
$scc = false;
if (!isset($pageTitle) || !is_string($pageTitle) || $pageTitle === '') {
    $pageTitle = null;
}
if (!isset($pageDescription)) {
    $pageDescription = null;
}
if (!isset($pageMetaKeywords)) {
    $pageMetaKeywords = null;
}
$defaultPageTitle = $pageTitle;
$app = Application::getFacadeApplication();
$config = $app->make('site')->getSite()->getConfigRepository();
$appConfig = $app->make('config');

if (is_object($c)) {
    $cp = new Permissions($c);
    $cID = $c->getCollectionID();
    $isEditMode = $c->isEditMode();
    $isArrangeMode = $c->isArrangeMode();
    $styleObject = false;

    /*
     * Handle page title
     *
     * We can set a title 3 ways:
     * 1. It comes through programmatically as $pageTitle. If this is the case then we pass it through, no questions asked
     * 2. It comes from meta title
     * 3. It comes from getCollectionName()
     * In the case of 3, we also pass it through page title format.
     */
    if ($pageTitle === null) {
        // we aren't getting it dynamically.
        $pageTitle = $c->getAttribute('meta_title');
        if (!is_string($pageTitle) || $pageTitle === '') {
            $seo = $app->make('helper/seo');
            if (!$seo->hasCustomTitle()) {
                $pageTitle = $c->getCollectionName();
                if ($c->isSystemPage()) {
                    $pageTitle = t($pageTitle);
                }
                $seo->addTitleSegmentBefore($pageTitle);
            }
            $seo->setSiteName(tc('SiteName', $app->make('site')->getSite()->getSiteName()));
            $seo->setTitleFormat($appConfig->get('concrete.seo.title_format'));
            $seo->setTitleSegmentSeparator($appConfig->get('concrete.seo.title_segment_separator'));
            $pageTitle = $seo->getTitle();
        }
    }

    if (!$pageDescription) {
        // we aren't getting it dynamically.
        $pageDescription = $c->getAttribute('meta_description');
        if (!$pageDescription) {
            $pageDescription = $c->getCollectionDescription();
        }
        $pageDescription = trim($pageDescription);
    }
    if (!$pageMetaKeywords) {
        $pageMetaKeywords = trim($c->getAttribute('meta_keywords'));
    }
    if ($c->hasPageThemeCustomizations()) {
        $styleObject = $c->getCustomStyleObject();
    } elseif (($pt = $c->getCollectionThemeObject()) && is_object($pt)) {
        $styleObject = $pt->getThemeCustomStyleObject();
    }
    if (isset($styleObject) && is_object($styleObject)) {
        $scc = $styleObject->getCustomCssRecord();
    }
} else {
    $cID = 1;
}
$metaTags = [];
$metaTags['charset'] = sprintf('<meta http-equiv="content-type" content="text/html; charset=%s"/>', APP_CHARSET);
if ($pageDescription) {
    $metaTags['description'] = sprintf('<meta name="description" content="%s"/>', htmlspecialchars($pageDescription, ENT_COMPAT, APP_CHARSET));
}
if ($pageMetaKeywords) {
    $metaTags['keywords'] = sprintf('<meta name="keywords" content="%s"/>', htmlspecialchars($pageMetaKeywords, ENT_COMPAT, APP_CHARSET));
}
if ($c->getAttribute('exclude_search_index')) {
    $metaTags['robots'] = sprintf('<meta name="robots" content="%s"/>', 'noindex');
}
$metaTags['generator'] = sprintf('<meta name="generator" content="%s"/>', 'concrete5' . ($appConfig->get('concrete.misc.app_version_display_in_header') ? ' - ' . APP_VERSION : null));
if (($modernIconFID = (int) $config->get('misc.modern_tile_thumbnail_fid')) && ($modernIconFile = File::getByID($modernIconFID))) {
    $metaTags['msapplication-TileImage'] = sprintf('<meta name="msapplication-TileImage" content="%s"/>', $modernIconFile->getURL());
    $modernIconBGColor = (string) $config->get('misc.modern_tile_thumbnail_bgcolor');
    if ($modernIconBGColor !== '') {
        $metaTags['msapplication-TileColor'] = sprintf('<meta name="msapplication-TileColor" content="%s"/>', $modernIconBGColor);
    }
}
$linkTags = [];
if (($favIconFID = (int) $config->get('misc.favicon_fid')) && ($favIconFile = File::getByID($favIconFID))) {
    $favIconFileURL = $favIconFile->getURL();
    $linkTags['shortcut icon'] = sprintf('<link rel="shortcut icon" href="%s" type="image/x-icon"/>', $favIconFileURL);
    $linkTags['icon'] = sprintf('<link rel="icon" href="%s" type="image/x-icon"/>', $favIconFileURL);
}
if (($appleIconFID = (int) $config->get('misc.iphone_home_screen_thumbnail_fid')) && ($appleIconFile = File::getByID($appleIconFID))) {
    $linkTags['apple-touch-icon'] = sprintf('<link rel="apple-touch-icon" href="%s"/>', $appleIconFile->getURL());
}

// Generate and dispatch an event, to let other Add-Ons make use of the available (meta) tags/page title
$event = new \Symfony\Component\EventDispatcher\GenericEvent();
$event->setArgument('metaTags', $metaTags);
$event->setArgument('linkTags', $linkTags);
$event->setArgument('pageTitle', $pageTitle);
$event->setArgument('defaultPageTitle', $defaultPageTitle);
Events::dispatch('on_header_required_ready', $event);
$metaTags = $event->getArgument('metaTags');
$linkTags = $event->getArgument('linkTags');
$pageTitle = $event->getArgument('pageTitle');
?>

<title><?php echo htmlspecialchars($pageTitle, ENT_COMPAT, APP_CHARSET); ?></title>

<?php
echo implode(PHP_EOL, $metaTags);
if (!empty($linkTags)) {
    echo implode(PHP_EOL, $linkTags);
} ?>

<script type="text/javascript">
    var CCM_DISPATCHER_FILENAME = "<?php echo DIR_REL . '/' . DISPATCHER_FILENAME; ?>";
    var CCM_CID = "<?php echo $cID ? $cID : 0; ?>";
    var CCM_EDIT_MODE = <?php echo $isEditMode ? 'true' : 'false'; ?>;
    var CCM_ARRANGE_MODE = <?php echo $isArrangeMode ? 'true' : 'false'; ?>;
    var CCM_IMAGE_PATH = "<?php echo ASSETS_URL_IMAGES; ?>";
    var CCM_TOOLS_PATH = "<?php echo REL_DIR_FILES_TOOLS_REQUIRED; ?>";
    var CCM_APPLICATION_URL = "<?php echo \Core::getApplicationURL(); ?>";
    var CCM_REL = "<?php echo \Core::getApplicationRelativePath(); ?>";
</script>

<?php
$v = View::getRequestInstance();
$u = new User();
if ($u->isRegistered()) {
    $v->requireAsset('core/account');
    $v->addFooterItem('<script type="text/javascript">$(function() { ccm_enableUserProfileMenu(); });</script>');
}
if (is_object($cp)) {
    View::element('page_controls_header', ['cp' => $cp, 'c' => $c]);
    $cih = $app->make('helper/concrete/ui');
    if ($cih->showNewsflowOverlay()) {
        $v->addFooterItem('<script type="text/javascript">$(function() { new ConcreteNewsflowDialog().open(); });</script>');
    }
    if (array_get($_COOKIE, 'ccmLoadAddBlockWindow') && $c->isEditMode()) {
        $v->addFooterItem('<script type="text/javascript">$(function() { setTimeout(function() { $("a[data-launch-panel=add-block]").click()}, 100); });</script>', 'CORE');
        setcookie("ccmLoadAddBlockWindow", false, -1, DIR_REL . '/');
    }
}
$v->markHeaderAssetPosition();
if (empty($disableTrackingCode)) {
    echo $config->get('seo.tracking.code.header');
}
if (isset($scc) && is_object($scc)) {
    ?>
    <style type="text/css"><?php echo $scc->getValue(); ?></style>
    <?php
}
echo $c->getAttribute('header_extra_content');
