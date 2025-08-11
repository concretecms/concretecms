<?php defined('C5_EXECUTE') or die("Access Denied.");
/**
 * Note – this came from a half-completed pull request, and no routes actually reference this controller anymore.
 * I'm keeping it around in case we decide to finish the functionality at some point.
 */
$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
?>
<div class="ccm-ui">

<?php if ($total == 0) { ?>

    <?php if (!$view->controller->getRequest()->isPost()) { ?>
        <?php echo t("There are no pages of this type added to your website.")?>
    <?php } ?>
<?php } else { ?>

    <form method="post" id="ccmUpdateFromPageTypeForm" data-dialog-form-processing="progressive" data-dialog-form="update-from-page-type" data-dialog-form-processing-title="<?=t('Update Defaults')?>" action="<?php echo $controller->action('submit')?>">

    <p><?php echo t('This will reset all blocks and their positions on child pages to those that are set in the defaults.')?></p>

    <div data-dialog-form-element="progress-bar"></div>

    <div class="dialog-buttons clearfix">
        <button class="btn btn-secondary" data-dialog-action="cancel"><?php echo t('Cancel')?></button>
        <button class="btn btn-primary float-end" data-dialog-action="submit"><?php echo t('Proceed')?></button>
    </div>

    </form>

<?php } ?>

</div>
