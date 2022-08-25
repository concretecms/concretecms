<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>


<div class="ccm-dashboard-header-buttons">
    <div class="btn-group">
        <a href="<?=URL::to('/dashboard/reports/health'); ?>" class="btn btn-secondary"><?php echo t('Back'); ?></a>
        <button class="btn btn-danger" data-launch-modal="delete-result" data-modal-options='{"title": "<?=t('Delete')?>"}'><?=t("Delete")?></button>
    </div>
</div>

<div class="d-none">
    <div data-modal-content="delete-result">
        <form method="post" action="<?php echo $view->action('delete'); ?>">
            <?php echo Loader::helper('validation/token')->output('delete'); ?>
            <input type="hidden" name="resultID" value="<?php echo $result->getID(); ?>">
            <p><?=t('Are you sure you want to delete this report result? This cannot be undone.')?></p>
            <div class="dialog-buttons">
                <button class="btn btn-secondary float-start" data-bs-dismiss="modal"><?=t('Cancel')?></button>
                <button class="btn btn-danger float-end" onclick="$('div[data-modal-content=delete-result] form').submit()"><?=t('Delete')?></button>
            </div>
        </form>
    </div>
</div>
