<?php
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Area\ContainerArea;

?>
<div class="area-content-accent">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <?php
                $area = new ContainerArea($container, 'Header');
                $area->display($c);
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-5">
                <?php
                $area = new ContainerArea($container, 'Column 1');
                $area->display($c);
                ?>
            </div>
            <div class="col-5 offset-2">
                <?php
                $area = new ContainerArea($container, 'Column 2');
                $area->display($c);
                ?>
            </div>
        </div>
    </div>
</div>
