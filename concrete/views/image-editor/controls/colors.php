<?php
    $view->requireAsset('core/colorpicker');
?>
<div class="row">
    <div class="col-sm-12 row">
        <div class="form-group">
            <label for="background-color" class="control-label"><?= t('Background Color'); ?></label>
            <div class="background-color-container">
                <input type="text" data-color-picker name="save-area-background-color" value="" id="ccm-colorpicker-save-area-background-color" />
            </div>
        </div>
    </div>
</div>
