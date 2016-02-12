<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="control-group">
    <label><?=$label?></label>
    <?php
    if (count($entities)) {
        foreach ($entities as $entity) { ?>
            <div class="checkbox">
                <label><input type="checkbox" name="express_association_<?=$control->getId()?>[]" value="<?=$entity->getId()?>"> <?=$formatter->getEntryDisplayName($control, $entity)?></label>
            </div>
        <?php 
        }
        ?>
    <?php 
    } else {
        ?>
        <p><?=t('None found.')?></p>
    <?php 
    } ?>
</div>