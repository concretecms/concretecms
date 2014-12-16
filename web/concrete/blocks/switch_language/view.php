<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-block-switch-language">

    <form method="post" action="<?=$view->action('switch_language')?>" class="form-inline">
        <?php echo $label?>
        <?php echo $form->select('language', $languages, $activeLanguage, array(
            'data-select' => 'multilingual-switch-language'
        ))?>
        <input type="hidden" name="currentPageID" value="<?php echo $cID?>" />
    </form>

</div>