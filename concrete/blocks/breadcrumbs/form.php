<?php
defined('C5_EXECUTE') or die('Access Denied.');
/** @var \Concrete\Core\Form\Service\Form $form */
$includeCurrent = isset($includeCurrent) && $includeCurrent ? 1 : 0;
$ignoreExcludeNav = isset($ignoreExcludeNav) && $ignoreExcludeNav ? 1 : 0;
$ignorePermission = isset($ignorePermission) && $ignorePermission ? 1 : 0;
?>
<div class="form-group">
    <div class="form-check form-switch">
        <?= $form->checkbox('includeCurrent', '1', $includeCurrent) ?>
        <?= $form->label('includeCurrent', t('Include Current Page in Breadcrumbs Navigation')) ?>
    </div>
</div>
<div class="form-group">
    <div class="form-check form-switch">
        <?= $form->checkbox('ignorePermission', '1', $ignorePermission) ?>
        <?= $form->label('ignorePermission', t('Ignore "View" permission')) ?>
    </div>
</div>
<div class="form-group">
    <div class="form-check form-switch">
        <?= $form->checkbox('ignoreExcludeNav', '1', $ignoreExcludeNav, ['aria-describedby' => 'ignoreExcludeNavHelp']) ?>
        <?= $form->label('ignoreExcludeNav', t('Ignore "Exclude From Nav" attribute')) ?>
        <div id="ignoreExcludeNavHelp"
             class="form-text"><?= t('If "Exclude From Nav" on, pages are excluded in Auto-Nav block. Usually, Breadcrumbs Navigation should ignore it and show these pages even if other navigation excludes them.') ?></div>
    </div>
</div>