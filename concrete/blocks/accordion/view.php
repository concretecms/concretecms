<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var \Concrete\Block\Accordion\AccordionEntry[] $entries
 */
/** @var int $bID */

$i = 0;
$flush = $flush ?? false;
$initialState = $initialState ?? null;
$itemHeadingFormat = $itemHeadingFormat ?? 'h2';
$alwaysOpen = $alwaysOpen ?? false;

?>

<div class="accordion ccm-block-accordion<?php if($flush){echo ' accordion-flush'; }?>" id="accordion<?=$bID?>">
    <?php
      foreach ($entries as $entry) {
        $i++;
        $entryClass = '';
        if (($initialState === 'openfirst' && $i == 1) || $initialState === 'open') {
          $entryClass = ' show';
        }
      ?>
        <div class="accordion-item">

            <<?php echo $itemHeadingFormat; ?> class="accordion-header">
                <button class="accordion-button <?php if($entryClass !== ' show'){echo 'collapsed'; }?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?=$entry->getId()?>">
                    <?=$entry->getTitle()?>
                </button>
            </<?php echo $itemHeadingFormat; ?>>

            <div id="collapse<?=$entry->getId()?>" class="accordion-collapse collapse <?php echo $entryClass; ?>" <?php if(!$alwaysOpen){ echo 'data-bs-parent="#accordion' . $bID . '"'; }?>>
                <div class="accordion-body">
                    <?=$entry->getDescription()?>
                </div>
            </div>

        </div>
    <?php } ?>
</div>
