<form action="<?=$view->action('save')?>" method='post'>
    <?=Loader::helper('form')->select('activeEditor', $editors, $active);?>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button type="submit" class="btn btn-primary pull-right"><?=t('Save')?></button>
        </div>
    </div>
</form>
