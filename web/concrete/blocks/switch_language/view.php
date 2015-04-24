<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-block-switch-language">

    <form method="post" class="form-inline">
        <?php echo $label?>
        <?php echo $form->select('language', $languages, $activeLanguage, array(
            'data-select' => 'multilingual-switch-language',
            'data-action' => $view->action('switch_language', $cID, '--language--')
        ))?>
    </form>

</div>