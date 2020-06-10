<?php
use Concrete\Core\Localization\Localization;

defined('C5_EXECUTE') or die('Access Denied.');

$c = Page::getCurrentPage();
$scc = false;
if (is_object($c)) {
    $cID = $c->getCollectionID();
    $styleObject = false;
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
    $c = null;
}
?>

<title><?=t('Preview')?></title>
<script type="text/javascript">
    var CCM_DISPATCHER_FILENAME = "<?= DIR_REL . '/' . DISPATCHER_FILENAME; ?>";
    var CCM_CID = <?= $cID ?? 0; ?>;
    var CCM_EDIT_MODE = false;
    var CCM_ARRANGE_MODE = false;
    var CCM_IMAGE_PATH = "<?= ASSETS_URL_IMAGES; ?>";
    var CCM_TOOLS_PATH = "<?= REL_DIR_FILES_TOOLS_REQUIRED; ?>";
    var CCM_APPLICATION_URL = "<?= rtrim((string) app('url/canonical'), '/'); ?>";
    var CCM_REL = "<?= app('app_relative_path'); ?>";
    var CCM_ACTIVE_LOCALE = <?= json_encode(Localization::activeLocale()) ?>;
</script>

<?php
$v = View::getRequestInstance();
$v->markHeaderAssetPosition();

if ($scc) {
    ?>
    <style type="text/css"><?= $scc->getValue(); ?></style>
    <?php
}
