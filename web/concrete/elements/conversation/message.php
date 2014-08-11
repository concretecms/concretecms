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
$c = Page::getByID($_REQUEST['cID']);
$cnvMessageURL = urlencode($c->getCollectionLink(true) . '#' . $cnvMessageID);

if ((!$message->isConversationMessageDeleted() && $message->isConversationMessageApproved()) || $message->conversationMessageHasActiveChildren()) {
	?>
	<div data-conversation-message-id="<?=$message->getConversationMessageID()?>" data-conversation-message-level="<?=$message->getConversationMessageLevel()?>" class="<?=$class?>">
		<a id="cnvMessage<?=$cnvMessageID?>" />
		<div class="ccm-conversation-message-user">
			<div class="ccm-conversation-avatar"><? print Loader::helper('concrete/avatar')->outputUserAvatar($ui)?></div>
			<div class="ccm-conversation-message-byline">
				<span class="ccm-conversation-message-username"><? if (!is_object($ui)) { ?><?=t('Anonymous')?><? } else { ?><?=$ui->getUserDisplayName()?><? } ?></span>
				<span class="ccm-conversation-message-divider">|</span>
				<span class="ccm-conversation-message-date"><?=$message->getConversationMessageDateTimeOutput($dateFormat);?></span>

                <?php if($canAdminMessage) { ?>
                    <span class="ccm-conversation-message-admin-control ccm-conversation-message-divider">|</span>
                    <span class="dropdown ccm-conversation-message-admin-control ">
                        <a class="dropdown-toggle" role="button" data-toggle="dropdown" href="#"><?=t('Edit')?></a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="drop5">
                            <li><a href="#" class="admin-edit" data-submit="edit-conversation-message"><?php echo t('Edit') ?></a></li>
                            <li><a href="#" class="admin-delete" data-submit="delete-conversation-message" data-conversation-message-id="<?=$message->getConversationMessageID()?>"><?=t('Delete')?></a></li>
                            <li><a href="#" class="admin-flag" data-submit="flag-conversation-message" data-conversation-message-id="<?=$message->getConversationMessageID()?>"><?=t('Flag As Spam')?></a></li>
                        </ul>
                    </span>
                <?php } ?>


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
						  		<i class="fa fa-search"></i>
						  	</div>
						  </div>
						  <div class="attachment-preview-container">
						 	 <img class="posted-attachment-image" src="<?php  echo $thumb->src; ?>" width="<?php  echo $thumb->width; ?>" height="<?php  echo $thumb->height; ?>" alt="attachment image" />
						  </div>
						 <?php } ?>
							<p class="<?php echo $paragraphPadding ?> filename" rel="<?php echo $attachment['cnvMessageAttachmentID'];?>"><a href="<?php echo $file->getDownloadURL() ?>"><?php echo $file->getFileName() ?></a>
                            <?
                            if (!$message->isConversationMessageDeleted() && $canAdminMessage) { ?>
                                <a rel="<?php echo $attachment['cnvMessageAttachmentID'];?>" class="attachment-delete ccm-conversation-message-control-icon ccm-conversation-message-admin-control" href="#"><i class="fa fa-trash-o"></i></a>
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
				<? if ($enablePosting && $displayMode == 'threaded') { ?>
					<li><a href="#" data-toggle="conversation-reply" data-post-parent-id="<?=$message->getConversationMessageID()?>"><?=t('Reply')?></a></li>
				<? } ?>
                <? if ($enableCommentRating) { ?>
                    <li><span class="ccm-conversation-message-divider">|</span></li>
                    <?
                    $ratingTypes = ConversationRatingType::getList();
                    foreach($ratingTypes as $ratingType) { ?>
                        <li><? echo $ratingType->outputRatingTypeHTML();?></li>
                    <? } ?>
                    <li><span class="ccm-conversation-message-rating-score" data-message-rating="<?=$message->cnvMessageID?>"><?=$message->getConversationMessageTotalRatingScore();?></span></li>
              <? } ?>
              <li class="ccm-conversation-social-share"><span class="ccm-conversation-message-divider">|</span></li>
              <li class="ccm-conversation-social-share">
                  <a class="ccm-conversation-message-control-icon" href="http://twitter.com/intent/tweet?url=<?php echo $cnvMessageURL?>" title="<?=t('Share message URL on Twitter.')?>"><i class="fa fa-twitter"></i></a>
              </li>
              <li class="ccm-conversation-social-share">
                  <a class="ccm-conversation-message-control-icon" href="http://www.facebook.com/sharer.php?u=<?php echo $cnvMessageURL?>" title="<?=t('Share message URL on Facebook.')?>"><i class="fa fa-facebook"></i></a>
              </li>
              <li class="ccm-conversation-social-share">
                  <a class="ccm-conversation-message-control-icon share-permalink" data-message-id= "<?php echo $messageID ?>" rel="<?php echo $cnvMessageURL ?>"  title="<?=t('Get message URL.')?>"data-dialog-title="<?php echo t('Link') ?>"  href="#"><i class="fa fa-link"></i></a>
              </li>

            </ul>
			<? } ?>

		</div>
	</div>
<? } ?>