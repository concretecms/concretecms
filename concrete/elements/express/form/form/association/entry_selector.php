<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php $entrySelector = \Concrete\Core\Support\Facade\Application::getFacadeApplication()->make('form/express/entry_selector'); ?>
<div class="mb-3">
    <?php if ($view->supportsLabel()) {
    ?>
        <label class="form-label" for="<?=$view->getControlID(); ?>"><?=$label; ?></label>
    <?php
} ?>
    <?php if ($view->isRequired()) { ?>
    <span class="text-muted small"><?=t('Required')?></span>
    <?php } ?>

    <?php
    $selectedEntity = null;
    if (!empty($selectedEntities)) {
        $selectedEntity = $selectedEntities[0];
    }
    echo $entrySelector->selectEntry($control->getAssociation()->getTargetEntity(), 'express_association_' . $control->getId(), $selectedEntity);
    ?>
</div>
