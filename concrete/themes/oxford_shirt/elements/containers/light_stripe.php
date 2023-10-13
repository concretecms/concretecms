<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Area\ContainerArea;

?>
<div class="stripe one-column bg-light">
    <?php
    $titleArea = new ContainerArea($container, 'Title');
    if ($c->isEditMode() || $titleArea->getTotalBlocksInArea($c) > 0) { ?>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10">
                    <div class="stripe-title">
                        <?php
                        $titleArea->display($c); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
    <?php
    $area = new ContainerArea($container, 'Body');
    $area->setAreaGridMaximumColumns(8);
    $area->enableGridContainer();;
    $area->display($c);
    ?>

</div>