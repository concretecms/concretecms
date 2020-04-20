<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php if (is_object($slot)) { ?>
<div class="ccm-block-dashboard-newsflow-latest-wrapper <?php if ($key == 'C') { ?>ccm-block-dashboard-newsflow-slot-c<?php } ?>">
    <div class="ccm-block-dashboard-newsflow-latest">
        <?=$slot->getContent()?>
    </div>
</div>
<?php 
}
