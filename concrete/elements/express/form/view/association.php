<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="list-group-item">
    <h6><?=$label?></h6>
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
