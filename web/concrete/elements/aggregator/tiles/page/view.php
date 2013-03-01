<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<? $page = $item->getCollectionObject(); ?>

<div class="ccm-aggregator-tile-headline"><a href="<?=Loader::helper('navigation')->getLinkToCollection($page)?>"><?=$page->getCollectionName()?></a></div>
<div><?=$item->getAggregatorItemPublicDateTime()?></div>
<p><?=$page->getCollectionDescription()?></p>