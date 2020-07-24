<?php defined('C5_EXECUTE') or die('Access Denied.');
$pageTypeDefaultTemplateID = $pagetype->getPageTypeDefaultPageTemplateID();
$pageTypePageTemplateObjects = $pagetype->getPageTypePageTemplateObjects();
?>

<p class="lead"><?php echo $pagetype->getPageTypeDisplayName(); ?></p>
    <table class="table table-striped">

<?php foreach ($pageTypePageTemplateObjects as $pt) {
    ?>


    <tr>
        <td style="width: 1px"><a href="<?php echo $view->action('edit_defaults', $pagetype->getPageTypeID(), $pt->getPageTemplateID()); ?>" target="_blank"><?php echo $pt->getPageTemplateIconImage(); ?></a></td>
        <td style="vertical-align: middle"><p class="lead" style="margin-bottom: 0px"><?php echo $pt->getPageTemplateDisplayName(); ?></p></td>
        <td style="width: 250px; vertical-align: middle">
            <div class="btn-group float-right">
                <a href="<?php echo $view->action('edit_defaults', $pagetype->getPageTypeID(), $pt->getPageTemplateID()); ?>" class="btn btn-secondary btn-sm"><?php echo t('Edit'); ?></a>
                <a class="btn btn-sm btn-secondary dialog-launch" dialog-title="<?php echo t('Update Defaults'); ?>"
                   dialog-modal="true"
                   dialog-width="500"
                   dialog-height="auto"
                   href="<?php echo URL::to('/ccm/system/dialogs/type/update_from_type/' . $pagetype->getPageTypeID() . '/' . $pt->getPageTemplateID()); ?>">
                    <?php echo t('Publish to Child Pages'); ?>
                </a></div>
        </td>
    </tr>

<?php
} ?>

</table>

<div class="ccm-dashboard-header-buttons">
    <a href="<?php echo URL::to('/dashboard/pages/types'); ?>" class="btn btn-secondary"><?php echo t('Back to List'); ?></a>
</div>
