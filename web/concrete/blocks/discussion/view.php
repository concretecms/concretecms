<?php defined('C5_EXECUTE') or die("Access Denied."); 
$c = Page::getCurrentPage();
if (is_object($discussion)) { 

	if ($enableNewConversations) { 
		$options = "{cnvDiscussionID: '{$discussion->getConversationDiscussionID()}', ctID: '{$ctID}', cParentID: '{$c->getCollectionID()}', posttoken: '{$posttoken}'}"; 
	} else {
		$options = "{cnvDiscussionID: '{$discussion->getConversationDiscussionID()}'}";
	}
	?>

	<div class="ccm-discussion" data-discussion-block-id="<?=$b->getBlockID()?>">

	<?
	if ($enableNewConversations) { ?>

		<div class="ccm-discussion-add-conversation" data-dialog-form="add-conversation">
			<?=Loader::element('conversation/discussion/form');?>
		</div>

		<button class="pull-right btn" data-action="add-conversation"><?=t('New Topic')?></button>


	<? } ?>

	</div>

	<script type="text/javascript">
	$(function() {
		$('div[data-discussion-block-id=<?=$b->getBlockID()?>]').ccmdiscussion(<?=$options?>);
	});
	</script>

<? }