<? defined('C5_EXECUTE') or die("Access Denied."); ?>

    <?=Core::make('helper/concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Image Uploading'), false, 'span8 offset2', false)?>

    <form method="post" id="file-access-extensions" action="<?=$view->action('save')?>" role="form">
        <?=$validation_token->output('image_uploading');?>

         <div class="checkbox">
            <label>
                <?=$form->checkbox('restrict_uploaded_image_sizes', '1', $restrict_uploaded_image_sizes)?>
                <span><?=t('Automatically resize uploaded images')?></span>
            </label>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button class="pull-right btn btn-primary" type="submit" value="file-access-extensions"><?=t('Save')?></button>
            </div>
        </div>	        
    </form>

    <?=Core::make('helper/concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>