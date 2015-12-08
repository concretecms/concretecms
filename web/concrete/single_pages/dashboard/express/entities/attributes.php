<?php defined('C5_EXECUTE') or die("Access Denied.");?>


<div class="row">
    <? View::element('dashboard/express/detail_navigation', array('entity' => $entity))?>
    <div class="col-md-8">
        <?php
        $list->render();
        ?>
    </div>
</div>
