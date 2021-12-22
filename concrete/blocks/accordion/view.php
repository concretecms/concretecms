<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
* @var $entries \Concrete\Block\Accordion\AccordionEntry[]
 */

$i =0;

?>

<div class="accordion ccm-block-accordion<?php if($flush){echo ' accordion-flush';}?>" id="accordion<?=$bID?>">

    <?php


    echo "Today is " . date("Ymdhit") . "<br>";



    foreach ($entries as $entry) {
      $i ++;
      $entryClass = '';
      if(($initialState == 'openfirst' && $i == 1) || $initialState == 'open') {
        $entryClass .= ' show';
      }
      ?>
        <div class="accordion-item">
            <<?php echo $itemHeadingFormat; ?> class="accordion-header">
                <button class="accordion-button <?php if($i != 1){echo 'collapsed';}?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?=$entry->getID()?>">
                    <?=$entry->getTitle()?>
                </button>
            </<?php echo $itemHeadingFormat; ?>>

            <div id="collapse<?=$entry->getID()?>" class="accordion-collapse collapse <?php echo $entryClass;?>" <?php if(!$alwaysOpen){ echo 'data-bs-parent="#accordion<?=$bID?>"';}?>>
                <div class="accordion-body">
                    <?=$entry->getDescription()?>
                </div>
            </div>

        </div>
    <?php } ?>
</div>
