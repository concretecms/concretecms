<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="form-group">
    <label class="control-label"><?=$label?></label>
    <?php
    if (!empty($entities)) {
        $selectedEntity = $selectedEntities[0];
        ?>
        <select class="form-control" name="express_association_<?=$control->getId()?>">
            <option value=""><?=t('** Choose %s', $control->getControlLabel())?></option>
            <?php
            foreach ($entities as $entity) {
                ?>
                <option
                    value="<?=$entity->getId()?>"
                    <?php if (is_object($selectedEntity) && $selectedEntity->getID() == $entity->getID()) { ?>selected<?php } ?>
                >
                    <?=$formatter->getEntryDisplayName($control, $entity)?>
                </option>
                <?php
            }
            ?>
        </select>
    <?php
    } else {
        ?><p><?=t('No entity found.')?></p><?php
    } ?>
</div>
