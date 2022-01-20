<?php
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Area\ContainerArea;

?>
<div class="stripe two-column-stripe bg-light">
    <div class="container">
        <div class="row gx-xl-10">
            <div class="col-md-6">
                <?php
                $area = new ContainerArea($container, 'Column 1');
                $area->display($c);
                ?>
            </div>
            <div class="col-md-6">
                <?php
                $area = new ContainerArea($container, 'Column 2');
                $area->display($c);
                ?>
            </div>
        </div>
    </div>
</div>