<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?=$html->css('discussion');?>
<table border="0" cellspacing="0" cellpadding="0" class="discussion-category-list">
<tr>
	<th>Categories</th>
	<th>Last Post</th>
	<th>Topics</th>
	<th>Messages</th>
</tr>
<? foreach($categories as $cat) { ?>
	<tr>
		<td class="discussion-category-name">
		<h2><a href="<?=$nav->getLinkToCollection($cat)?>"><?=$cat->getCollectionName()?></a></h2>
		<p><?=$cat->getCollectionDescription()?></p>
		</td>
		<td class="discussion-category-last-post"><?
			$post = $cat->getLastPost();
			if (is_object($post)) { 
		?><a href="<?=$nav->getLinkToCollection($post)?>"><?=$post->getCollectionName()?></a><br/>By <strong><?=$post->getUserName()?></strong>
		<? } ?>
		</td>
		<td><?=number_format($cat->getTotalTopics())?></td>
		<td><?=number_format($cat->getTotalPosts())?></td>
	</tr>
<? } ?>
</table>