<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-block-switch-language">

    <form method="post" class="form-inline">
        <?= $label ?>
        <?= $form->select(
            'language',
            $languages,
            $activeLanguage,
            [
                'data-select' => 'multilingual-switch-language',
                'data-action' => $view->action('switch_language', $cID, '--language--'),
            ]
        ) ?>
    </form>

</div>