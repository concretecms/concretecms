<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div id="ccm-theme-preview-frame-wrapper">
<iframe class="ccm-page-preview-frame" name="ccm-theme-preview-frame"
    src="<?=URL::to('/ccm/system/panels/details/theme/do_preview', $pThemeID, $skinIdentifier, $previewPage->getCollectionID())?>">
</iframe>
</div>

<div class="ccm-panel-detail-form-actions">
    <button class="float-right btn btn-success" type="button" data-panel-detail-action="submit"><?= t('Save Changes') ?></button>
</div>

