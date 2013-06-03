<?
defined('C5_EXECUTE') or die("Access Denied.");
$im = Loader::helper('image');
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
		<a id="cnvMessage<?=$cnvMessageID?>" />
		<div class="ccm-conversation-message-user">
			<div class="ccm-conversation-avatar"><? print Loader::helper('concrete/avatar')->outputUserAvatar($ui)?></div>
			<div class="ccm-conversation-message-byline">
				<span class="ccm-conversation-message-username"><? if (!is_object($ui)) { ?><?=t('Anonymous')?><? } else { ?><?=$ui->getUserDisplayName()?><? } ?></span>
				<span class="ccm-conversation-message-divider">|</span>
				<span class="ccm-conversation-message-date"><?=$message->getConversationMessageDateTimeOutput();?></span>
			</div>
			
		</div>
		<div class="ccm-conversation-message-body">
			<?=$message->getConversationMessageBodyOutput()?>
		</div>
		<div class="ccm-conversation-message-controls">
			<div class="message-attachments">
				<?php
				if(count($message->getAttachments($message->cnvMessageID))) {
					foreach ($message->getAttachments($message->cnvMessageID) as $attachment) { ?>
						<div class="attachment-container">
						<?php $file = File::getByID($attachment['fID']);
						if(is_object($file)) {
							if(strpos($file->getMimeType(), 'image') !== false) {
								$paragraphPadding = 'image-preview';
								$thumb = $im->getThumbnail($file, '90', '90', true); ?>
						  <div class="image-popover-hover">
						  	<div class="glyph-container">
						  		<i class="icon-search icon-white"></i>
						  	</div>
						  </div>
						  <img class="posted-attachment-image" src="<?php  echo $thumb->src; ?>" width="<?php  echo $thumb->width; ?>" height="<?php  echo $thumb->height; ?>" alt="attachment image" />
						 <?php } ?>
							<p class="<?php echo $paragraphPadding ?>" rel="<?php echo $attachment['cnvMessageAttachmentID'];?>"><a href="<?php echo $file->getDownloadURL() ?>"><?php echo $file->getFileName() ?></a>
							<? if (!$message->isConversationMessageDeleted()) { ?>
								<a rel="<?php echo $attachment['cnvMessageAttachmentID'];?>" class="attachment-delete ccm-conversation-message-admin-control" href="#"><?=t('Delete')?></a>
							<?php } ?>
							</p>
						</div>
					<?php }
					$paragraphPadding = '';
					}
				} ?>
			</div>
			<? if (!$message->isConversationMessageDeleted() && $message->isConversationMessageApproved()) { ?>
			<ul>
				<!-- <li class="ccm-conversation-message-admin-control"><a href="#" data-submit="flag-conversation-message" data-conversation-message-id="<?=$message->getConversationMessageID()?>"><?=t('Flag As Spam')?></a></li>
				<li class="ccm-conversation-message-admin-control"><a href="#" data-submit="delete-conversation-message" data-conversation-message-id="<?=$message->getConversationMessageID()?>"><?=t('Delete')?></a></li> -->
				
				<? if ($enablePosting && $displayMode == 'threaded') { ?>
					<li><a href="#" data-toggle="conversation-reply" data-post-parent-id="<?=$message->getConversationMessageID()?>"><?=t('Reply')?></a></li>
				<? } ?>
			</ul>
			<span class="control-divider"> | </span>
			<? } ?>
			
		<? Loader::element('conversation/social_share', array('cID' => $cID, 'message' => $message));?>
		<? if ($enableCommentRating) {
			$ratingTypes = ConversationRatingType::getList();
			foreach($ratingTypes as $ratingType) {
				echo $ratingType->outputRatingTypeHTML();
			} ?>
			<span class="ccm-conversation-message-rating-score" data-message-rating="<?=$message->cnvMessageID?>"><?=$message->getConversationMessageTotalRatingScore();?></span>
		<? } ?>
		</div>
	</div>
<? } ?>
