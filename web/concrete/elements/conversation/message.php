<?
defined('C5_EXECUTE') or die("Access Denied.");
$im = Loader::helper('image');

// this is TEMPORARY. we will be using dedicated permissions for this.
$u = new User();
$canAdminMessage = ($u->isSuperUser() || $u->inGroup(Group::getByID(ADMIN_GROUP_ID)));

$ui = $message->getConversationMessageUserObject();
$class = 'ccm-conversation-message ccm-conversation-message-level' . $message->getConversationMessageLevel();
if ($message->isConversationMessageDeleted()) {
	$class .= ' ccm-conversation-message-deleted';
}

if($dateFormat == 'custom' && $customDateFormat) {
	$dateFormat = array($customDateFormat);
}
if (!$message->isConversationMessageApproved()){
	$class .= ' ccm-conversation-message-flagged';
}
$cnvMessageID = $message->cnvMessageID;
if ((!$message->isConversationMessageDeleted() && $message->isConversationMessageApproved()) || $message->conversationMessageHasActiveChildren()) {
	?>
	<div data-conversation-message-id="<?=$message->getConversationMessageID()?>" data-conversation-message-level="<?=$message->getConversationMessageLevel()?>" class="<?=$class?> ccm-ui">
		<?php if($canAdminMessage) { ?>
		<ul class="nav nav-pills cnv-admin-pane pull-right">
			<li class="dropdown">
			<a class="dropdown-toggle" id="drop4" role="button" data-toggle="dropdown" href="#">&#x25bc;</a>
				<ul class="dropdown-menu" role="menu" aria-labelledby="drop5">
				<li><a href="#" class="admin-best-answer"><?php echo t('Best Answer') ?></a></li>
				<li><a href="#" class="admin-promote"><?php echo t('Promote') ?></a></li>
				<li><a href="#" class="admin-edit" data-submit="edit-conversation-message"><?php echo t('Edit') ?></a></li> 
				<li><a href="#" class="admin-delete" data-submit="delete-conversation-message" data-conversation-message-id="<?=$message->getConversationMessageID()?>"><?=t('Delete')?></a></li>
				<li><a href="#" class="admin-flag" data-submit="flag-conversation-message" data-conversation-message-id="<?=$message->getConversationMessageID()?>"><?=t('Flag As Spam')?></a></li>
				</ul>
			</li>
		</ul>
		<?php } ?>
		<a id="cnvMessage<?=$cnvMessageID?>" />
		<div class="ccm-conversation-message-user">
			<div class="ccm-conversation-avatar"><? print Loader::helper('concrete/avatar')->outputUserAvatar($ui)?></div>
			<div class="ccm-conversation-message-byline">
				<span class="ccm-conversation-message-username"><? if (!is_object($ui)) { ?><?=t('Anonymous')?><? } else { ?><?=$ui->getUserDisplayName()?><? } ?></span>
				<span class="ccm-conversation-message-divider">|</span>
				<span class="ccm-conversation-message-date"><?=$message->getConversationMessageDateTimeOutput($dateFormat);?></span>
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
						  <div class="image-popover-hover" data-full-image="<?php echo $file->getURL() ?>">
						  	<div class="glyph-container">
						  		<i class="fa faicon-search fa-white"></i>
						  	</div>
						  </div>
						  <div class="attachment-preview-container">
						 	 <img class="posted-attachment-image" src="<?php  echo $thumb->src; ?>" width="<?php  echo $thumb->width; ?>" height="<?php  echo $thumb->height; ?>" alt="attachment image" />
						  </div>
						 <?php } ?>
							<p class="<?php echo $paragraphPadding ?> filename" rel="<?php echo $attachment['cnvMessageAttachmentID'];?>"><a href="<?php echo $file->getDownloadURL() ?>"><?php echo $file->getFileName() ?></a>
							<? 
							if (!$message->isConversationMessageDeleted() && $canAdminMessage) { ?>
								<a rel="<?php echo $attachment['cnvMessageAttachmentID'];?>" class="attachment-delete ccm-conversation-message-admin-control" href="#"><?=t('Delete')?></a>
							<?php } ?>
							<br />
							<a class="download" href="<?php echo $file->getDownloadURL() ?>"><?php echo t('Download') ?></a>
							</p>
						</div>
					<?php }
					$paragraphPadding = '';
					}
				} ?>
			</div>
			<? if (!$message->isConversationMessageDeleted() && $message->isConversationMessageApproved()) { ?>
			<ul class="standard-message-controls">
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
