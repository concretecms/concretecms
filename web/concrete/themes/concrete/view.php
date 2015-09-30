<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php $view->inc('elements/header.php'); ?>

<div class="container">
<div class="row">
<div class="col-sm-10 col-sm-offset-1">
<?php Loader::element('system_errors', array('format' => 'block', 'error' => $error, 'success' => $success, 'message' => $message)); ?>
</div>
</div>

<?php print $innerContent ?>

</div>
</div>

<?php $view->inc('elements/footer.php'); ?>
