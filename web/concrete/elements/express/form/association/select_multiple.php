<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="control-group">
    <label><?=$label?></label>
    <?php
    if (count($entities)) {
        foreach ($entities as $entity) {
            ?>
            <label><input type="checkbox" value="<?=$entity->getId()?>"> <?=$formatter->getEntryDisplayName($control, $entity)?></label>
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