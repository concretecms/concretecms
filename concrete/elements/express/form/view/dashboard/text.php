<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="mb-3">

<?php if ($control->getHeadline()) { ?>
    <div class="form-label"><?=$control->getHeadline()?></div>
<?php } ?>
<?php if ($control->getBody()) { ?>
    <div>
        <?=$control->getBody()?>
    </div>
<?php } ?>

</div>