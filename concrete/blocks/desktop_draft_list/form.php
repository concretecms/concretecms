<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="form-group">
    <?= $form->label('draftsPerPage', t('Number of drafts per page'), ['class' => 'form-label']); ?>
    <?= $form->number('draftsPerPage', $draftsPerPage, ['placeholder' => $defaultDraftsPerPage]); ?>
</div>
