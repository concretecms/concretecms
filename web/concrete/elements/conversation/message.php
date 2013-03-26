<?
defined('C5_EXECUTE') or die("Access Denied.");
$ui = $message->getConversationMessageUserObject();
$class = 'ccm-conversation-message ccm-conversation-message-level' . $message->getConversationMessageLevel();
if ($message->isConversationMessageDeleted()) {
	$class .= ' ccm-conversation-message-deleted';
}
if (!$message->isConversationMessageApproved()){
	$class .= ' ccm-conversation-message-flagged';
}
$cnvMessageID = $message->cnvMessageID;
if ((!$message->isConversationMessageDeleted() && $message->isConversationMessageApproved()) || $message->conversationMessageHasActiveChildren()) {
	?>
	<div data-conversation-message-id="<?=$message->getConversationMessageID()?>" data-conversation-message-level="<?=$message->getConversationMessageLevel()?>" class="<?=$class?>">
		<a name="cnvMessage<?=$cnvMessageID?>" />
		<div class="ccm-conversation-message-user">
			<div class="ccm-conversation-avatar"><? print Loader::helper('concrete/avatar')->outputUserAvatar($ui)?></div>
			<div class="ccm-conversation-message-byline"><? if (!is_object($ui)) { ?><?=t('Anonymous')?><? } else { ?><?=$ui->getUserDisplayName()?><? } ?></div>
		</div>
		<div class="ccm-conversation-message-body">
			<?=$message->getConversationMessageBodyOutput()?>
		</div>
		<div class="ccm-conversation-message-controls">
			<div class="message-attachments">
				<?php
				if(count($message->getAttachments($message->cnvMessageID))) {
					foreach ($message->getAttachments($message->cnvMessageID) as $attachment) {
						$file = File::getByID($attachment['fID']);
						if(is_object($file)) { ?>
						<p rel="<?php echo $attachment['cnvMessageAttachmentID'];?>"><a href="<?php echo $file->getDownloadURL() ?>"><?php echo $file->getFileName() ?></a>
							<? if (!$message->isConversationMessageDeleted()) { ?>
								<a rel="<?php echo $attachment['cnvMessageAttachmentID'];?>" class="attachment-delete ccm-conversation-message-admin-control" href="#">Delete</a>
							<?php } ?>
						</p>
					<?php }
					}
				} ?>
			</div>
			<? if (!$message->isConversationMessageDeleted() && $message->isConversationMessageApproved()) { ?>
			<ul>
				<li class="ccm-conversation-message-admin-control"><a href="#" data-submit="flag-conversation-message" data-conversation-message-id="<?=$message->getConversationMessageID()?>"><?=t('Flag As Spam')?></a></li>
				<li class="ccm-conversation-message-admin-control"><a href="#" data-submit="delete-conversation-message" data-conversation-message-id="<?=$message->getConversationMessageID()?>"><?=t('Delete')?></a></li>
				
				<? if ($enablePosting && $displayMode == 'threaded') { ?>
					<li><a href="#" data-toggle="conversation-reply" data-post-parent-id="<?=$message->getConversationMessageID()?>"><?=t('Reply')?></a></li>
				<? } ?>
			</ul>
			<? } ?>
			
		<?=$message->getConversationMessageDateTimeOutput()?>
		<? if ($enableCommentRating) {
			$ratingTypes = ConversationRatingType::getList();
			foreach($ratingTypes as $ratingType) { ?>
				<?=$ratingType->outputRatingTypeHTML();?>
				<?=$message->getConversationMessageRating($ratingType); ?>
			 <? } ?>
		<? } ?>
		</div>
	</div>
	<?php
}
?>
