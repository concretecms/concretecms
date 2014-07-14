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


// we make sure that our active theme gets registered as well because we want to make sure that
// assets provided by the theme aren't loaded by the block in this mode.
$pt = $c->getCollectionThemeObject();
$pt->registerAssets();
$bv->render('view');