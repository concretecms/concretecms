<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

    <?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Allowed File Types'), false, 'span8 offset2', false)?>

    <form method="post" id="file-access-extensions" action="<?=$view->action('file_access_extensions')?>" role="form">
        <?=$validation_token->output('file_access_extensions');?>
        <p>
            <?=t('Only files with the following extensions will be allowed. Separate extensions with commas. Periods and spaces will be ignored.')?>
        </p>
        <div class="form-group">
            <textarea name="file-access-file-types" class="form-control" rows="3"><?=$file_access_file_types?></textarea>
        </div>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button class="pull-right btn btn-primary" type="submit" value="file-access-extensions"><?=t('Save')?></button>
            </div>
        </div>	        
    </form>

    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>