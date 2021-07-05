<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="<?=$view->getThemePath()?>/main.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    View::element('header_required', array('pageTitle' => isset($pageTitle) ? $pageTitle : ''));
    ?>
</head>
<body>
<div class="ccm-ui">

<?php echo $innerContent ?>

</div>

<?php
View::element('footer_required');
?>
</body>
</html>