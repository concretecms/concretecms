<? defined('C5_EXECUTE') or die("Access Denied.");

$set = $b->getCustomStyleSet();
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
    'set' => $set
));

$pt = $c->getCollectionThemeObject();
$pt->registerAssets();
$bv->render('view');