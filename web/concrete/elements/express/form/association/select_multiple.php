<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="control-group">
    <label><?=$label?></label>
    <?php
    if (!empty($entities)) {
        foreach ($entities as $entity) {
            ?>
            <div class="checkbox">
                <label>
                    <input
                        type="checkbox"
                        <?php if (is_array($selectedEntities) && in_array($entity, $selectedEntities)) { ?>checked<?php } ?>
                        name="express_association_<?=$control->getId()?>[]"
                        value="<?=$entity->getId()?>"
                    >
                    <?=$formatter->getEntryDisplayName($control, $entity)?>
                </label>
            </div>
            <?php
        }
    } else {
        ?><p><?=t('No entity found.')?></p><?php
    }
    ?>
</div>
