<?php defined('C5_EXECUTE') or die("Access Denied.");

$set = $b->getCustomStyle();
$btHandle = $b->getBlockTypeHandle();
if ($btHandle === BLOCK_HANDLE_SCRAPBOOK_PROXY) {
    $bx = Block::getByID($b->getController()->getOriginalBlockID());
    if (is_object($bx)) {
        $btHandle = $bx->getBlockTypeHandle();
    }
}
if (is_object($set) && isset($styleHeader)) { ?>
    
    <script type="text/javascript">
        $('head').append('<style type="text/css"><?=addslashes($styleHeader)?></style>');
    </script>
<?php

}

$pt = $c->getCollectionThemeObject();

$blockClasses = $pt->getThemeBlockClasses();
$customClasses = $blockClasses[$btHandle] ?? [];

if (isset($blockClasses['*'])) {
    $customClasses = array_unique(array_merge($customClasses, $blockClasses['*']));
}

$enableBlockContainer = -1;
if ($pt->supportsGridFramework() && $b->overrideBlockTypeContainerSettings()) {
    if ($b->enableBlockContainer()) {
        $enableBlockContainer = 1;
    } else {
        $enableBlockContainer = 0;
    }
}

$gf = $pt->getThemeGridFrameworkObject();

if (Config::get('concrete.design.enable_custom')) {
    Loader::element('custom_style', array(
        'page' => $c,
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
}
else {
    Loader::element('custom_block_template', array(
        'page' => $c,
        'saveAction' => $controller->action('submit'),
        'resetAction' => $controller->action('reset'),
        'style' => $b->getCustomStyle(true),
        'bFilename' => $bFilename,
        'templates' => $templates,
    ));
}


$pt->registerAssets();
$bv->render('view');
