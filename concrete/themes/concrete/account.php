<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php $view->inc('elements/header.php'); ?>

<div class="container">
<div class="row">
<div class="col-sm-10 col-sm-offset-1">
<?php
View::element(
    'system_errors',
    array(
        'format' => 'block',
        'error' => isset($error) ? $error : null,
        'success' => isset($success) ? $success : null,
        'message' => isset($message) ? $message : null,
    )
);
?>
</div>
</div>

<div class="mt-5 mb-5 row justify-content-center">
    <div class="col-10">
        <h1 class="display-4"><?=t('My Account')?></h1>
        <hr>
    </div>
    <div class="col-6">
        
        <?php echo $innerContent ?>
    </div>
    <div class="col-4">
        <?php
        $nav->render();
        ?>
    </div>
</div>

<?php $view->inc('elements/footer.php'); ?>
