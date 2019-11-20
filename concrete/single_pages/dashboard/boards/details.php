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

        
    </div>
</div>
