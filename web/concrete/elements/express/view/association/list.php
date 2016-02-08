<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="control-group">
    <div>
    <label><?=$label?></label>
    </div>
    <?php
    if (count($entities)) {
        ?>
        <?php foreach ($entities as $entity) {
    ?>
            <div><?=$formatter->getEntryDisplayName($control, $entity)?></div>
        <?php 
}
        ?>
    <?php 
    } ?>
</div>