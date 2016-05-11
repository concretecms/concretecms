<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="control-group">
    <label><?=$label?></label>
    <?php
    if (count($entities)) {
        ?>
        <select class="form-control" name="express_association_<?=$control->getId()?>">
            <option value=""><?=t('** Choose %s', $control->getControlLabel())?></option>
        <?php foreach ($entities as $entity) {
    ?>
            <option value="<?=$entity->getId()?>" <?php if (is_array($selectedEntities) && in_array($entity, $selectedEntities)) { ?>selected<?php } ?> ><?=$formatter->getEntryDisplayName($control, $entity)?></option>
        <?php 
}
        ?>
        </select>
    <?php 
    } else {
        ?>
        <p><?=t('No entity found.')?></p>
    <?php 
    } ?>
</div>