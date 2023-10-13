<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Area\ContainerArea;

?>
<div class="stripe one-column">
    <?php
    $area = new ContainerArea($container, 'Body');
    if ($c->isEditMode() || $area->getTotalBlocksInArea($c) > 0) { ?>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10">
                    <?php
                    $area->display($c);
                    ?>
                </div>
            </div>
        </div>
    <?php
    }
    ?>

</div>