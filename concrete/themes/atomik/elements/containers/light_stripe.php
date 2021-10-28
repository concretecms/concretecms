<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Area\ContainerArea;

?>
<div class="stripe one-column bg-light">
    <?php
    $titleArea = new ContainerArea($container, 'Title');
    if ($c->isEditMode() || $titleArea->getTotalBlocksInArea($c) > 0) { ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
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
    $area->setAreaGridMaximumColumns(12);
    $area->enableGridContainer();;
    $area->display($c);
    ?>

</div>