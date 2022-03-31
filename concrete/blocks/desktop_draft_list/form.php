<?php defined('C5_EXECUTE') or die('Access Denied.');
/** @var \Concrete\Core\Form\Service\Form $form */
?>
<div class="form-group">
    <?= $form->label('draftsPerPage', t('Number of drafts per page')); ?>
    <?= $form->number('draftsPerPage', $draftsPerPage ?? 10, ['placeholder' => $defaultDraftsPerPage ?? 10]); ?>
</div>
