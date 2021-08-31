<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
* @var $entries \Concrete\Block\Accordion\AccordionEntry[]
 */
?>

<div class="accordion ccm-block-accordion" id="accordion<?=$bID?>">
    <?php foreach ($entries as $entry) { ?>
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?=$entry->getID()?>">
                    <?=$entry->getTitle()?>
                </button>
            </h2>
            <div id="collapse<?=$entry->getID()?>" class="accordion-collapse collapse" <?php /* data-bs-parent="#accordion<?=$bID?>" */ ?>>
                <div class="accordion-body">
                    <?=$entry->getDescription()?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
