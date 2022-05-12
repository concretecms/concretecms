<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var Concrete\Core\ConcreteCms\ActivitySlotItem|null $slot */
/** @var string|null $key */
?>
<?php if (is_object($slot)) { ?>
<div class="ccm-block-dashboard-concrete-latest-wrapper <?php if ($key == 'C') { ?>ccm-block-dashboard-concrete-slot-c<?php } ?>">
    <div class="ccm-block-dashboard-concrete-latest">
        <?=$slot->getContent()?>
    </div>
</div>
<?php
}
