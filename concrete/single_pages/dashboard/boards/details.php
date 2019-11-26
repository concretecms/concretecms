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
        <?php
            if (!$board->getDateLastRefreshed()) { ?>
        
                <div class="alert alert-info"><?=t('This board has not been published. Once you have added data sources, make sure you publish your board.')?></div>
                
            <?php }
        ?>

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
