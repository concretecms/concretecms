<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php $view->inc('elements/header.php'); ?>


<div class="container ccm-page-account">
    <?php // Note, we have to put the ccm-page-account classes on the container elements themselves, instead of a
    // an outer wrapper, because our theme uses special styling to make top level container elements have extra
    // spacing. If we add an element between the container and the outer page class it breaks this. ?>
    <div class="row">
        <div class="col-md-12">
            <h1><?=$c->getCollectionName()?></h1>

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
</div>

<div class="container ccm-page-account">
    <div class="row gx-10">
        <div class="col-md-8">
            <?php echo $innerContent ?>
        </div>
        <div class="col-md-4">
            <?php
            $nav->render();
            ?>
        </div>
    </div>
</div>

<?php $view->inc('elements/footer.php'); ?>
