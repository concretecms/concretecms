<?php

defined('C5_EXECUTE') or die("Access Denied.");

$pt = $c->getCollectionThemeObject();

$areaClasses = $pt->getThemeAreaClasses();
$areaHandle = $a->getTopLevelAreaHandle();
$customClasses = $areaClasses[$areaHandle] ?? [];

if (isset($areaClasses['*'])) {
    $customClasses = array_unique(array_merge($customClasses, $areaClasses['*']));
}

$gf = $pt->getThemeGridFrameworkObject();

Loader::element("custom_style", array(
    'page' => $c,
    'saveAction' => $controller->action('submit'),
    'resetAction' => $controller->action('reset'),
    'customClasses' => $customClasses,
    'gf' => $gf,
    'style' => $c->getAreaCustomStyle($a, true),
));
