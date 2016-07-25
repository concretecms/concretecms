<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<p class="lead"><?php echo $pagetype->getPageTypeDisplayName(); ?></p>

    <table class="table table-striped">

<?php foreach ($pagetype->getPageTypePageTemplateObjects() as $pt) {
    ?>


    <tr>
        <td><a href="<?php echo $view->action('edit_defaults', $pagetype->getPageTypeID(), $pt->getPageTemplateID());
    ?>" target="_blank"><?php echo $pt->getPageTemplateIconImage();
    ?></a></td>
        <td style="width: 100%; vertical-align: middle"><p class="lead" style="margin-bottom: 0px"><?php echo $pt->getPageTemplateDisplayName();
    ?></p></td>
        <td style="vertical-align: middle"><a href="<?php echo $view->action('edit_defaults', $pagetype->getPageTypeID(), $pt->getPageTemplateID());
    ?>" class="btn btn-default"><?php echo t('Edit Defaults');
    ?></a></td>
    </tr>

<?php 
} ?>

</table>

<div class="ccm-dashboard-header-buttons">
    <a href="<?php echo URL::to('/dashboard/pages/types'); ?>" class="btn btn-default"><?php echo t('Back to List'); ?></a>
</div>