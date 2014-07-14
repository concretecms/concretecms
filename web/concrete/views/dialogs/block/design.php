<? defined('C5_EXECUTE') or die("Access Denied.");

$csr = $b->getBlockCustomStyleRule();
if (is_object($csr)) {
    $styleHeader = '#' . $csr->getCustomStyleRuleCSSID(
            1) . ' {' . $csr->getCustomStyleRuleText() . "}";  ?>
    <script type="text/javascript">
        $('head').append('<style type="text/css"><?=addslashes($styleHeader)?></style>');
    </script>
<?
}

$ag = \Concrete\Core\Http\ResponseAssetGroup::get();
$ag->requireAsset('core/style-customizer');
Loader::element("custom_style");

$pt = $c->getCollectionThemeObject();
$pt->registerAssets();
$bv->render('view');