<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="list-group-item">

<?php if ($control->getHeadline()) { ?>
    <h3><?=$control->getHeadline()?></h3>
<?php } ?>
<?php if ($control->getBody()) { ?>
    <?=$control->getBody()?>
<?php } ?>

</div>
