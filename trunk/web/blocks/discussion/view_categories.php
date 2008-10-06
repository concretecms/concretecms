<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div class="discussion-categories">
<? foreach($categories as $cat) { ?>

	<div class="discussion-category">
		<h2><a href="<?=$nav->getLinkToCollection($cat)?>"><?=$cat->getCollectionName()?></a></h2>
		<p><?=$cat->getCollectionDescription()?></p>
		
		<?=$cat->getTotalViews()?> View<?= $cat->getTotalViews() == 1 ? '' : 's';?>
		|
		<?=$cat->getTotalTopics()?> Topic<?= $cat->getTotalTopics() == 1 ? '' : 's';?>
		|
		<?=$cat->getTotalMessages()?> Message<?= $cat->getTotalMessages() == 1 ? '' : 's';?>
		
	</div>

<? } ?>
</div>