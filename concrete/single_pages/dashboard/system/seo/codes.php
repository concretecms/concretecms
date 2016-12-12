<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<p class="help-block"><?php echo t('Any HTML you paste here will be inserted at either the bottom or top of every page in your website automatically.'); ?></p>

<form id="tracking-code-form" action="<?php echo $view->action(''); ?>" method="post">
    <?php echo Core::make('helper/validation/token')->output('update_tracking_code'); ?>

    <div class="form-group">
        <?php echo $form->label('tracking_code_header', t('Header Tracking Codes')); ?>
        <?php echo $form->textarea('tracking_code_header', $tracking_code_header, array('style' => 'height: 250px;')); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('tracking_code_footer', t('Footer Tracking Codes')); ?>
        <?php echo $form->textarea('tracking_code_footer', $tracking_code_footer, array('style' => 'height: 250px;')); ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button type="submit" class="btn btn-primary pull-right" name="tracking-code-form"><?php echo t('Save'); ?></button>
        </div>
    </div>
</form>
