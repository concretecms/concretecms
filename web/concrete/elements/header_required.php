<?php
defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();
$cp = false;
$isEditMode = false;
$isArrangeMode = false;
$scc = false;
$defaultPageTitle = isset($pageTitle) && $pageTitle ? $pageTitle : null;

if (is_object($c)) {
    $cp = new Permissions($c);
    $cID = $c->getCollectionID();
    $isEditMode = $c->isEditMode();
    $isArrangeMode = $c->isArrangeMode();
    $styleObject = false;

    /*
     * Handle page title
     */

    // We can set a title 3 ways:
    // 1. It comes through programmatically as $pageTitle. If this is the case then we pass it through, no questions asked
    // 2. It comes from meta title
    // 3. It comes from getCollectionName()
    // In the case of 3, we also pass it through page title format.

    if (!$defaultPageTitle) {
        // we aren't getting it dynamically.
        $pageTitle = $c->getCollectionAttributeValue('meta_title');
        if (!$pageTitle) {
            $pageTitle = $c->getCollectionName();
            if ($c->isSystemPage()) {
                $pageTitle = t($pageTitle);
            }
            $seo = Core::make('helper/seo');
            if (!$seo->hasCustomTitle()) {
                $seo->addTitleSegmentBefore($pageTitle);
            }
            $seo->setSiteName(tc('SiteName', Config::get('concrete.site')));
            $seo->setTitleFormat(Config::get('concrete.seo.title_format'));
            $seo->setTitleSegmentSeparator(Config::get('concrete.seo.title_segment_separator'));
            $pageTitle = $seo->getTitle();
        }
    }

    if (!isset($pageDescription) || !$pageDescription) {
        // we aren't getting it dynamically.
        $pageDescription = $c->getAttribute('meta_description');
        if (!$pageDescription) {
            $pageDescription = $c->getCollectionDescription();
        }
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
    if (!isset($pageTitle)) {
        $pageTitle = null;
    }
}
$metaTags = array();
$metaTags['charset'] = sprintf('<meta http-equiv="content-type" content="text/html; charset=%s"/>', APP_CHARSET);
if (trim($pageDescription) != '') {
    $metaTags['description'] = sprintf('<meta name="description" content="%s"/>', htmlspecialchars($pageDescription, ENT_COMPAT, APP_CHARSET));
}
$pageMetaKeywords = !isset($pageMetaKeywords) || !$pageMetaKeywords ? $c->getCollectionAttributeValue('meta_keywords') : $pageMetaKeywords;
if (trim($pageMetaKeywords) != ''){
    $metaTags['keywords'] = sprintf('<meta name="keywords" content="%s"/>', htmlspecialchars($pageMetaKeywords, ENT_COMPAT, APP_CHARSET));
}
if ($c->getCollectionAttributeValue('exclude_search_index')) {
    $metaTags['robots'] = sprintf('<meta name="robots" content="%s"/>', 'noindex');
}
$metaTags['generator'] = sprintf('<meta name="generator" content="%s"/>', 'concrete5' . (Config::get('concrete.misc.app_version_display_in_header') ? ' - ' . APP_VERSION : null));
if (($modernIconFID = intval(Config::get('concrete.misc.modern_tile_thumbnail_fid'))) && ($modernIconFile = File::getByID($modernIconFID)) && is_object($modernIconFile)) {
    $metaTags['msapplication-TileImage'] = sprintf('<meta name="msapplication-TileImage" content="%s"/>', $modernIconFile->getURL());
    $modernIconBGColor = strval(Config::get('concrete.misc.modern_tile_thumbnail_bgcolor'));
    if (strlen($modernIconBGColor)) {
        $metaTags['msapplication-TileColor'] = sprintf('<meta name="msapplication-TileColor" content="%s"/>', $modernIconBGColor);
    }
}
$linkTags = array();
if (($favIconFID = intval(Config::get('concrete.misc.favicon_fid'))) && ($favIconFile = File::getByID($favIconFID)) && is_object($favIconFile)) {
    $favIconFileURL = $favIconFile->getURL();
    $linkTags['shortcut icon'] = sprintf('<link rel="shortcut icon" href="%s" type="image/x-icon"/>', $favIconFileURL);
    $linkTags['icon'] = sprintf('<link rel="icon" href="%s" type="image/x-icon"/>', $favIconFileURL);
}
if (($appleIconFID = intval(Config::get('concrete.misc.iphone_home_screen_thumbnail_fid'))) && ($appleIconFile = File::getByID($appleIconFID)) && is_object($appleIconFile)) {
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
$v = View::getInstance();
$u = new User();
if ($u->isRegistered()) {
    $v->requireAsset('core/account');
    $v->addFooterItem('<script type="text/javascript">$(function() { ccm_enableUserProfileMenu(); });</script>');
}
if (is_object($cp)) {
    View::element('page_controls_header', array('cp' => $cp, 'c' => $c));
    $cih = Core::make('helper/concrete/ui');
    if ($cih->showNewsflowOverlay()) {
        $v->addFooterItem('<script type="text/javascript">$(function() { new ConcreteNewsflowDialog().open(); });</script>');
    }
    if (array_get($_COOKIE, 'ccmLoadAddBlockWindow') && $c->isEditMode()) {
        $v->addFooterItem('<script type="text/javascript">$(function() { setTimeout(function() { $("a[data-launch-panel=add-block]").click()}, 100); });</script>', 'CORE');
        setcookie("ccmLoadAddBlockWindow", false, -1, DIR_REL . '/');
    }
}
$v->markHeaderAssetPosition();
if (empty($disableTrackingCode) && Config::get('concrete.seo.tracking.code_position') === 'top') {
    echo Config::get('concrete.seo.tracking.code');
}
if (isset($scc) && is_object($scc)) {
    ?>
    <style type="text/css"><?php echo $scc->getValue(); ?></style>
    <?php
}
echo $c->getAttribute('header_extra_content');
