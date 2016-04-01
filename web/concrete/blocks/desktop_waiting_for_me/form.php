<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="form-group">
    <?php echo $form->label('linkToPage', t('Link To Page'));?>
    <?=Core::make("helper/form/page_selector")->selectPage('linkToPage', $linkToPage);?>
</div>
