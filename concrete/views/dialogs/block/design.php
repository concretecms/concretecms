<?php defined('C5_EXECUTE') or die("Access Denied.");

$set = $b->getCustomStyle();
$btHandle = $b->getBlockTypeHandle();
if ($btHandle == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
    $bx = Block::getByID($b->getController()->getOriginalBlockID());
    if (is_object($bx)) {
        $btHandle = $bx->getBlockTypeHandle();
    }
}
if (is_object($set)) {
    ?>
    <script type="text/javascript">
        $('head').append('<style type="text/css"><?=addslashes($styleHeader)?></style>');
    </script>
<?php

}

$ag = \Concrete\Core\Http\ResponseAssetGroup::get();
$ag->requireAsset('core/style-customizer');
$pt = $c->getCollectionThemeObject();

$blockClasses = $pt->getThemeBlockClasses();
$customClasses = array();
if (isset($blockClasses[$btHandle])) {
    $customClasses = $blockClasses[$btHandle];
}

if (isset($blockClasses['*'])) {
    $customClasses = array_unique(array_merge($customClasses, $blockClasses['*']));
}

$enableBlockContainer = -1;
if ($pt->supportsGridFramework() && $b->overrideBlockTypeContainerSettings()) {
    $enableBlockContainer = $b->enableBlockContainer();
}

$gf = $pt->getThemeGridFrameworkObject();

Loader::element("custom_style", array(
    'saveAction' => $controller->action('submit'),
    'resetAction' => $controller->action('reset'),
    'style' => $b->getCustomStyle(true),
    'bFilename' => $bFilename,
    'bName' => $b->getBlockName(),
    'displayBlockContainerSettings' => $pt->supportsGridFramework(),
    'enableBlockContainer' => $enableBlockContainer,
    'gf' => $gf,
    'templates' => $templates,
    'customClasses' => $customClasses,
    'canEditCustomTemplate' => $canEditCustomTemplate,
));

$pt->registerAssets();
$bv->render('view');
