<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?=$html->css('discussion');?>
<table border="0" cellspacing="0" cellpadding="0" class="discussion-category-list">
<tr>
	<th colspan="2">Topics</th>
	<th>Posted By</th>
	<th>Replies</th>
</tr>
<? foreach($topics as $cat) { ?>
	<tr <? if ($cat->isPostPinned()) { ?> class="discussion-post-pinned" <? } ?>>
		<td><?
			$ui = $cat->getUserObject();
			print $av->outputUserAvatar($ui);
		?></td>
		<td class="discussion-category-name">
		<h2><a href="<?=$nav->getLinkToCollection($cat)?>"><?=$cat->getCollectionName()?></a></h2>
		<p><?=$cat->getCollectionDescription()?></p>
		</td>
		<td class="discussion-category-last-post"><?
			print $cat->getUserName() . ' on<br/>' . date('M d, Y', strtotime($cat->getCollectionDateAdded())) . ' at<br/>' . date('g:i A', strtotime($cat->getCollectionDateAdded()));
		?></td>
		<td><?=number_format($cat->getTotalReplies())?></td>
	</tr>
<? } ?>
</table>