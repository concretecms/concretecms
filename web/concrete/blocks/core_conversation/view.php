<?php defined('C5_EXECUTE') or die("Access Denied.");
$paginate = ($paginate) ? 'true' : 'false';
$itemsPerPage = ($paginate) ? $itemsPerPage : -1;

if (is_object($conversation)) {
?>
<div class="ccm-conversation-wrapper" data-conversation-id="<?=$conversation->getConversationID()?>">
<?=t('Loading Conversation')?> <img src="<?=Loader::helper('concrete/urls')->getBlockTypeAssetsURL($b->getBlockTypeObject(), 'loading.gif')?>" />
</div>

<script type="text/javascript">
$(function() {
	$('div[data-conversation-id=<?=$conversation->getConversationID()?>]').ccmconversation({
		cnvID: <?=$conversation->getConversationID()?>,
		blockID: <?=$bID?>,
		posttoken: '<?=$posttoken?>',
		displayMode: '<?=$displayMode?>',
		paginate: <?=$paginate?>,
		itemsPerPage: <?=$itemsPerPage?>,
		orderBy: '<?=$orderBy?>',
		enableOrdering: <?=$enableOrdering?>,
		displayPostingForm: '<?=$displayPostingForm?>',
		insertNewMessages: '<?=$insertNewMessages?>',
		activeUsers: <?=Loader::helper('json')->encode($users)?>,
		enableCommentRating: <?=$enableCommentRating?>
	});
});
</script>
<? } ?>