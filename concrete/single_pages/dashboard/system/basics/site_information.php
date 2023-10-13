<?php
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
if ($u->isSuperUser()) {
?>
<form action="<?= $view->action('submit') ?>" method="post">
    <?php
    $token->output('submit');
    ?>

    <?=$survey->render(); ?>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions d-flex">
            <button type="submit" class="btn btn-primary ms-auto"><?=t('Save')?></button>
        </div>
    </div>
</form>
<?php } else { ?>
    <?=t('Only the super user may modify site information.')?>
<?php } ?>
