<?php defined('C5_EXECUTE') or die("Access Denied."); 

use Concrete\Core\Board\Template\TemplateSlot;
?>

<div class="container p-5 bg-light">
    <div class="row mb-4">
        <div class="col-4">
            <?php
            (new TemplateSlot($template, 1))->display();
            ?>
        </div>
        <div class="col-4">
            <?php
            (new TemplateSlot($template, 2))->display();
            ?>
        </div>
        <div class="col-4">
            <?php
            (new TemplateSlot($template, 3))->display();
            ?>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-4">
            <?php
            (new TemplateSlot($template, 4))->display();
            ?>
        </div>
        <div class="col-4">
            <?php
            (new TemplateSlot($template, 5))->display();
            ?>
        </div>
        <div class="col-4">
            <?php
            (new TemplateSlot($template, 6))->display();
            ?>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-4">
            <?php
            (new TemplateSlot($template, 7))->display();
            ?>
        </div>
        <div class="col-4">
            <?php
            (new TemplateSlot($template, 8))->display();
            ?>
        </div>
        <div class="col-4">
            <?php
            (new TemplateSlot($template, 9))->display();
            ?>
        </div>
    </div>
</div>
