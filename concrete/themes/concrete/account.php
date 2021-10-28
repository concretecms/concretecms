<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php $view->inc('elements/header.php'); ?>

<div class="container ccm-page-account">
<div class="row">
<div class="col-sm-10 offset-sm-1">
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

<div class="row">
    <div class="col-sm-10 offset-sm-1">
        <h1 class="display-4"><?=t('My Account')?></h1>

        <div class="row">
            <div class="col-sm-8">

                <?php echo $innerContent ?>
            </div>
            <div class="col-sm-4">
                <?php
                $nav->render();
                ?>
            </div>
        </div>
    </div>
</div>
</div>

<?php $view->inc('elements/footer.php'); ?>
