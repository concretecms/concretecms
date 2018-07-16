<?php defined('C5_EXECUTE') or die('Access Denied.');
$pageTypeDefaultTemplateID = $pagetype->getPageTypeDefaultPageTemplateID();
?>

<p class="lead"><?php echo $pagetype->getPageTypeDisplayName(); ?></p>
    <table class="table table-striped">
    <?php foreach ($pagetype->getPageTypePageTemplateObjects() as $pt) {
        $defaultTemplate = $pageTypeDefaultTemplateID == $pt->getPageTemplateID();
        ?>
        <tr <?php echo $defaultTemplate ? 'style="background: #d9edf7;"' : ''; ?>>
            <td style="width: 1px;">
                <a href="<?php echo $view->action('edit_defaults', $pagetype->getPageTypeID(), $pt->getPageTemplateID()); ?>"
                    target="_blank"><?php echo $pt->getPageTemplateIconImage(); ?></a>
            </td>
            <td style="vertical-align: middle;">
                <p class="lead" style="margin-bottom: 0;"><?php echo $pt->getPageTemplateDisplayName() . ($defaultTemplate ? ' (' . tc('PageTemplate', 'Default') . ')' : ''); ?></p>
            </td>
            <td style="vertical-align: middle;">
            <a href="<?php echo $view->action('edit_defaults', $pagetype->getPageTypeID(), $pt->getPageTemplateID()); ?>"
                class="btn btn-default btn-sm pull-right"><?php echo t('Edit'); ?></a>
            </td>
        </tr>
    <?php
    } ?>
</table>

<div class="ccm-dashboard-header-buttons">
    <a href="<?php echo URL::to('/dashboard/pages/types'); ?>" class="btn btn-default"><?php echo t('Back to List'); ?></a>
</div>
