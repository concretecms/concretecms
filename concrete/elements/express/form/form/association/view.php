<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="form-group">
    <div>
    <label class="control-label"><?=$label?></label>
    </div>
    <?php
    if (isset($selectedEntities) && is_array($selectedEntities)) {
        $entities = $selectedEntities;
    }
    if (count($entities)) {
        ?>
        <ul>
        <?php foreach ($entities as $entity) {
    ?>
            <li><a href="<?=URL::to('/dashboard/express/entries', 'view_entry', $entity->getID())?>"><?=$formatter->getEntryDisplayName($control, $entity)?></a></li>
        <?php 
}
        ?>
        </ul>
    <?php 
    } ?>
</div>