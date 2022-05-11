<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var $entries \Concrete\Block\Accordion\AccordionEntry[]
 * @var int $bID
 */

$i = 0;
$flush = $flush ?? false;
$initialState = $initialState ?? null;
$itemHeadingFormat = $itemHeadingFormat ?? 'h2';
$alwaysOpen = $alwaysOpen ?? false;

?>

<div class="accordion ccm-block-accordion <?php if($flush){ echo ' accordion-flush'; }?>" id="accordion<?=$bID?>">
    <?php
      foreach ($entries as $entry) {
        $i ++;
        $entryClass = '';
        if (($initialState === 'openfirst' && $i == 1) || $initialState === 'open') {
          $entryClass = ' show';
        }
      ?>
        <div class="accordion-item">

            <<?php echo $itemHeadingFormat; ?> class="accordion-header">
                <a href="javascript:void(0)" class="accordion-button <?php if($entryClass !== ' show'){echo 'collapsed'; }?>" data-bs-toggle="collapse" data-bs-target="#collapse<?=$entry->getID()?>">
                    <?=$entry->getTitle()?>
                </a>
            </<?php echo $itemHeadingFormat; ?>>

            <div id="collapse<?=$entry->getID()?>" class="accordion-collapse collapse <?php echo $entryClass;?>" <?php if(!$alwaysOpen){ echo 'data-bs-parent="#accordion' .$bID.'"';}?>>
                <div class="accordion-body">
                    <?=$entry->getDescription()?>
                </div>
            </div>

        </div>
    <?php } ?>
</div>
