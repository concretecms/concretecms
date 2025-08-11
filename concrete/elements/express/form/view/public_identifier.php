<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php if ($entry) { ?>
<div class="list-group-item">
    <h6><?=$label?></h6>
    <div>
        <?=$entry->getPublicIdentifier()?>
    </div>
</div>
<?php } ?>