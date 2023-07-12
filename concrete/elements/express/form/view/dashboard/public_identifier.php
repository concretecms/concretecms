<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php if ($entry) { ?>
<div class="mb-3">
    <label class="form-label"><?=$label?></label>
    <div>
        <?=$entry->getPublicIdentifier()?>
    </div>
</div>
<?php } ?>