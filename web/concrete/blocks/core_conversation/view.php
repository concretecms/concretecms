<?php defined('C5_EXECUTE') or die("Access Denied.");
$paginate = ($paginate) ? 'true' : 'false';
$itemsPerPage = ($paginate) ? $itemsPerPage : -1;
$blockAreaHandle = $this->block->getAreaHandle();
$iph = Loader::helper('validation/ip');
$commentRatingIP = ip2long($iph->getRequestIP());
$u = new User();
if ($u->isLoggedIn()) {
	$uID = $u->getUserID();
	$maxFileSize = $maxFileSizeRegistered;
	$maxFiles = $maxFilesRegistered;
}else{
	$maxFileSize = $maxFileSizeGuest;
	$maxFiles = $maxFilesGuest;
	$uID = 0;
}

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
			cID: <?=$cID?>,
			posttoken: '<?=$posttoken?>',
			displayMode: '<?=$displayMode?>',
			addMessageLabel: '<?=$addMessageLabel?>',
			paginate: <?=$paginate?>,
			itemsPerPage: <?=$itemsPerPage?>,
			orderBy: '<?=$orderBy?>',
			enableOrdering: <?=$enableOrdering?>,
			displayPostingForm: '<?=$displayPostingForm?>',
			insertNewMessages: '<?=$insertNewMessages?>',
			activeUsers: <?=Loader::helper('json')->encode($users)?>,
			enableCommentRating: <?=$enableCommentRating?>,
			commentRatingUserID: <?=$uID?>,
			commentRatingIP: '<?=$commentRatingIP?>',
			dateFormat: '<?=$dateFormat?>',
			blockAreaHandle: '<?=$blockAreaHandle ?>',
			fileExtensions: '<?=$fileExtensions?>',
			maxFileSize: '<?=$maxFileSize?>',
			maxFiles: '<?=$maxFiles?>'
		});
	});
	</script>
<? } ?>