<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div class="row">
    <div class="col-4">
        <?php
        $element = Element::get('dashboard/boards/menu', ['board' => $board, 'action' => 'details']);
        $element->render();
        ?>
    </div>
    <div class="col-8">
        <div>
            <h3><?=t('Name')?></h3>
            <p><?=$board->getBoardName()?></p>
        </div>
        <div>
            <h3><?=t('Template')?></h3>
            <p><?=$template->getName()?></p>
        </div>

        <div>
            <h3><?=t('Total Slots')?></h3>
            <p><?=$templateDriver->getTotalSlots()?></p>
        </div>

    </div>
</div>
