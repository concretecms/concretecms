<?php
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Area\ContainerArea;

?>
<div class="container">
    <div class="row gx-xl-10">
        <div class="col-md-3 col-6">
            <?php
            $area = new ContainerArea($container, 'Item 1');
            $area->display($c);
            ?>
        </div>
        <div class="col-md-3 col-6">
            <?php
            $area = new ContainerArea($container, 'Item 2');
            $area->display($c);
            ?>
        </div>
        <div class="col-md-3 col-6">
            <?php
            $area = new ContainerArea($container, 'Item 3');
            $area->display($c);
            ?>
        </div>
        <div class="col-md-3 col-6">
            <?php
            $area = new ContainerArea($container, 'Item 4');
            $area->display($c);
            ?>
        </div>
    </div>
</div>