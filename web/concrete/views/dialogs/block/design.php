<? defined('C5_EXECUTE') or die("Access Denied.");

$set = $b->getCustomStyle();
if (is_object($set)) { ?>
    <script type="text/javascript">
        $('head').append('<style type="text/css"><?=addslashes($styleHeader)?></style>');
    </script>
<?
}

$ag = \Concrete\Core\Http\ResponseAssetGroup::get();
$ag->requireAsset('core/style-customizer');
Loader::element("custom_style", array(
    'action' => $controller->action('submit'),
    'style' => $b->getCustomStyle()
));

$pt = $c->getCollectionThemeObject();
$pt->registerAssets();
$bv->render('view');