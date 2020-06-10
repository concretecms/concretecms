<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php $view->inc('elements/header.php'); ?>

<div class="main-container">
    <div class="main-container-inner">
        <div class="row">
            <div class="col-sm-12">
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
            <div class="col-sm-12">
                <?php echo $innerContent ?>
            </div>
        </div>
    </div>
</div>

<?php $view->inc('elements/footer.php'); ?>
