<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php

use Concrete\Core\Express\Component\ExpressEntrySelectInstanceFactory;

$options = [];
$selectedIDs = [];
if (isset($selectedEntities)) {
    foreach ($selectedEntities as $selectedEntity) {
        $options[] = [
            'exEntryID' => $selectedEntity->getID(),
            'label' => $selectedEntity->getLabel(),
        ];
        $selectedIDs[] = $selectedEntity->getID();
    }
}

$targetEntityHandle = $control->getAssociation()->getTargetEntity()->getHandle();
$factory = app(ExpressEntrySelectInstanceFactory::class);
$instance = $factory->createInstance($targetEntityHandle);

?>
<div class="mb-3">
    <?php if ($view->supportsLabel()) {
    ?>
        <label class="form-label" for="<?=$view->getControlID(); ?>"><?=$label; ?></label>
    <?php
} ?>
    <?php if ($view->isRequired()) { ?>
        <span class="text-muted small"><?=t('Required')?></span>
    <?php } ?>

    <div data-vue="cms">
        <concrete-express-entry-select
                :entry-id='<?=json_encode($selectedIDs)?>'
                input-name="express_association_<?= $control->getId(); ?>[]"
                access-token="<?=$instance->getAccessToken()?>"
                entity="<?=$targetEntityHandle?>">
        </concrete-express-entry-select>
    </div>
</div>