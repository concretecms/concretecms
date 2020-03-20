<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$icons = Core::make(\Concrete\Core\Page\Container\IconRepository::class)->getIcons();

if (isset($container) && $container) {
    $action = $view->url('/dashboard/pages/containers/update_container', $container->getContainerID());
    $buttonText = t('Save');
    $containerName = $container->getContainerName();
    $containerHandle = $container->getContainerHandle();
    $containerIcon = $container->getContainerIcon();
} else {
    $action = $view->url('/dashboard/pages/containers/add', 'add_container');
    $buttonText = t('Add');
    $containerName = '';
    $containerHandle = '';
    $containerIcon = false;
}

?>


<form method="post" action="<?=$action?>">
    <?=$view->controller->token->output($tokenMessage)?>
    
    <div class="form-group">
        <label for="containerName" class="control-label"><?=t('Name')?></label>
        <?=$form->text('containerName', $containerName)?>
    </div>

    <div class="form-group">
        <label for="containerHandle" class="control-label"><?=t('Handle')?></label>
        <?=$form->text('containerHandle', $containerHandle)?>
        <div class="form-text text-muted"><?=t("This must be alpha-numeric. Underscores are allowed. It should match a template file that ends in .php")?></div>
    </div>
    
    <div class="form-group">
        <label for="containerIcon" class="control-label"><?=t('Icon')?></label>
        <div class="row">
            <?php
            $i = 0;
            foreach ($icons as $icon) {
                $isChecked = false;
                if (!$containerIcon) {
                    $isChecked = ($i == 0);
                } else {
                    $isChecked = $containerIcon == $icon->getFilename();
                }
                ?>
                <div class="col-2">
                    <label style="">
                        <img src="<?=$icon->getUrl()?>" class="img-fluid" style="" />
                        <div class="text-center">
                            <?=$form->radio('containerIcon', $icon->getFilename(), $isChecked)?>
                        </div>
                    </label>
                </div>
                <?php ++$i;
                ?>
                <?php
            } ?>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=$view->url('/dashboard/pages/containers')?>" class="btn btn-secondary float-left"><?=t("Cancel")?></a>
            <button type="submit" class="btn float-right btn-primary"><?=$buttonText?></button>
            <?php if ($container) { ?>
                <button type="button" class="btn float-right btn-danger mr-1" data-toggle="modal" data-target="#delete-container"><?=t('Delete Container')?></button>
            <?php } ?>
        </div>
    </div>
</form>

<?php if ($container) { ?>

    <div class="modal fade" id="delete-container" tabindex="-1">
        <form method="post" action="<?=$view->action('delete_container', $container->getContainerID())?>">
            <?=$token->output('delete_container')?>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?=t('Delete Container')?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <svg><use xlink:href="#icon-dialog-close" /></svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?=t('Are you sure you want to remove this container? Content will be lost anywhere it is used on your site. This cannot be undone.')?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal"><?=t('Cancel')?></button>
                        <button type="submit" class="btn btn-danger float-right"><?=t('Delete Container')?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>

<?php } ?>
