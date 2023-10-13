<?php
defined('C5_EXECUTE') or die("Access Denied.");

$view->inc('elements/header.php');
?>

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <?php
                View::element('system_errors', [
                    'format' => 'block',
                    'error' => isset($error) ? $error : null,
                    'success' => isset($success) ? $success : null,
                    'message' => isset($message) ? $message : null,
                ]);

                echo $innerContent;
                ?>
            </div>
        </div>
    </div>

<?php
$view->inc('elements/footer.php');
