<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div class="row">
    <div class="col-4">
        <?php
        $element = Element::get('dashboard/boards/menu', ['board' => $board]);
        $element->render();
        ?>
    </div>
    <div class="col-8">

        <h3 class="font-weight-light"><?=t('Add Data Source')?></h3>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-button="attribute-type" data-toggle="dropdown">
                <?=t('Choose Type')?> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <?php
                foreach ($sources as $source) {
                    /**
                     * @var \Concrete\Core\Entity\Board\DataSource $source
                     */
                    $driver = $source->getDriver();
                    $formatter = $driver->getIconFormatter();
                    ?>
                    <li><a class="dropdown-item" href="#"><?=$formatter->getListIconElement()?> <?=$source->getName()?></a></li>
                    <?php
                }
                ?>
            </ul>
        </div>

        
    </div>
</div>
