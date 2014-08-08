<? defined('C5_EXECUTE') or die("Access Denied.");

$ag = \Concrete\Core\Http\ResponseAssetGroup::get();
$ag->requireAsset('core/style-customizer');

$pt = $c->getCollectionThemeObject();

$areaClasses = $pt->getThemeAreaClasses();
$customClasses = array();
if (isset($areaClasses[$a->getAreaHandle()])) {
    $customClasses = $areaClasses[$a->getAreaHandle()];
}

Loader::element("custom_style", array(
    'saveAction' => $controller->action('submit'),
    'resetAction' => $controller->action('reset'),
    'customClasses' => $customClasses,
    'style' => $c->getAreaCustomStyle($a, true)
));