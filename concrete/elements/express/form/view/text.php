<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php if ($control->getHeadline()) { ?>
    <h6><?=$control->getHeadline()?></h6>
<?php } ?>
<?php if ($control->getBody()) { ?>
    <div>
        <?=$control->getBody()?>
    </div>
<?php } ?>
