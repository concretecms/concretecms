<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-aggregator-tile-headline"><a href="<?=$item->getURL()?>"><?=$item->getTitle()?></a></div>
<div><?=$item->getAggregatorItemPublicDateTime()?></div>
<p><?=$item->getDescription()?></p>