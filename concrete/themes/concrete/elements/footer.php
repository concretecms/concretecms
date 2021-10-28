<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php
if (isset($c) && is_object($c)) {
    Loader::element('footer_required');
} else {
    View::getInstance()->markFooterAssetPosition();
}
?>

<script type="text/javascript" src="<?=$view->getThemePath()?>/main.js"></script>

</body>
</html>
