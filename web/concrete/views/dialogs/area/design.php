<? defined('C5_EXECUTE') or die("Access Denied.");

$ag = \Concrete\Core\Http\ResponseAssetGroup::get();
$ag->requireAsset('core/style-customizer');
Loader::element("custom_style", array(
    'saveAction' => $controller->action('submit'),
    'resetAction' => $controller->action('reset'),
    'style' => $c->getAreaCustomStyle($a, true)
));