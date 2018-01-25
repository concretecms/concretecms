<?php defined('C5_EXECUTE') or die('Access Denied.');

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$im = $app->make('helper/image');

$mp = new Permissions($message);
$canDeleteMessage = $mp->canDeleteConversationMessage();
$canFlagMessage = $mp->canFlagConversationMessage();
$canEditMessage = $mp->canEditConversationMessage();
$canRateMessage = $mp->canRateConversationMessage();

$ui = $message->getConversationMessageUserObject();
$class = 'message ccm-conversation-message ccm-conversation-message-level' . $message->getConversationMessageLevel();
if ($message->isConversationMessageDeleted()) {
    $class .= ' ccm-conversation-message-deleted';
}

if (!isset($dateFormat)) {
    $dateFormat = 'default';
} elseif ($dateFormat == 'custom' && $customDateFormat) {
    $dateFormat = array($customDateFormat);
}
if (!$message->isConversationMessageApproved()) {
    $class .= ' ccm-conversation-message-flagged';
}

$cnvMessageID = $message->getConversationMessageID();
$cnvID = $message->getConversationID();
$c = Page::getByID(\Request::request('cID'));
$cnvMessageURL = urlencode($c->getCollectionLink(true) . '#cnv' . $cnvID . 'Message' . $cnvMessageID);

if ((!$message->isConversationMessageDeleted() && $message->isConversationMessageApproved()) || $message->conversationMessageHasActiveChildren()) {
    $author = $message->getConversationMessageAuthorObject();
    $formatter = $author->getFormatter();
    ?>
	<div data-conversation-message-id="<?=$message->getConversationMessageID()?>" data-conversation-message-level="<?=$message->getConversationMessageLevel()?>" class="<?=$class?>">
		<a id="cnv<?=$cnvID?>Message<?=$cnvMessageID?>"></a>
		<div class="ccm-conversation-message-user">
			<div class="ccm-conversation-avatar"><?php echo $formatter->getAvatar(); ?></div>
			<div class="ccm-conversation-message-byline">
				<span class="ccm-conversation-message-username"><?php echo $formatter->getDisplayName(); ?></span>
				<span class="ccm-conversation-message-divider">|</span>
				<span class="ccm-conversation-message-date"><?=$message->getConversationMessageDateTimeOutput($dateFormat); ?></span>

                <?php if ($canDeleteMessage || $canFlagMessage) { ?>
                    <span class="ccm-conversation-message-admin-control ccm-conversation-message-divider">|</span>
                    <?php if ($canEditMessage) { ?>
                        <span class="ccm-conversation-message-admin-control ccm-conversation-message-divider"><a href="javascript:void(0)" class="admin-edit"
                        data-conversation-message-id="<?=$message->getConversationMessageID()?>" data-load="edit-conversation-message"><?php echo t('Edit') ?></a></span>
                    <?php } ?>
                    <?php if ($canDeleteMessage) { ?>
                        <span class="ccm-conversation-message-admin-control ccm-conversation-message-divider"><a href="#" class="admin-delete" data-submit="delete-conversation-message"
                        data-conversation-message-id="<?=$message->getConversationMessageID()?>"><?=t('Delete')?></a></span>
                    <?php } ?>
                    <?php if ($canFlagMessage) { ?>
                        <span class="ccm-conversation-message-admin-control ccm-conversation-message-divider"><a href="#" class="admin-flag" data-submit="flag-conversation-message"
                        data-conversation-message-id="<?=$message->getConversationMessageID()?>"><?=t('Flag As Spam')?></a></span>
                    <?php } ?>
                <?php } ?>
			</div>
		</div>

        <?php if ($review = $message->getConversationMessageReview()) { ?>
            <div class="ccm-conversation-review">
                <?php
                View::element('conversation/message/review', [
                    'review' => $message->getConversationMessageReview(),
                    'starsOnly' => true
                ]);
                ?>
            </div>
        <?php } ?>

		<div class="ccm-conversation-message-body">
			<?=$message->getConversationMessageBodyOutput()?>
		</div>

		<div class="ccm-conversation-message-controls">
			<div class="message-attachments">
				<?php
                if (count($message->getAttachments($message->getConversationMessageID()))) {
                    foreach ($message->getAttachments($message->getConversationMessageID()) as $attachment) { ?>
						<div class="attachment-container">
						<?php
                        $file = File::getByID($attachment['fID']);
                        if (is_object($file)) {
                            if (strpos($file->getMimeType(), 'image') !== false) {
                                $paragraphPadding = 'image-preview';
                                $thumb = $im->getThumbnail($file, '90', '90', true);
                                ?>
                                <div class="image-popover-hover" data-full-image="<?php echo $file->getURL() ?>">
                                    <div class="glyph-container"><i class="fa fa-search"></i></div>
                                </div>
                                <div class="attachment-preview-container">
                                    <img class="posted-attachment-image" src="<?php  echo $thumb->src;
                                    ?>" width="<?php  echo $thumb->width;
                                    ?>" height="<?php  echo $thumb->height;
                                    ?>" alt="attachment image" />
                                </div>
                            <?php } ?>
							<p class="<?php echo $paragraphPadding ?> filename" rel="<?php echo $attachment['cnvMessageAttachmentID']; ?>">
                                <a href="<?php echo $file->getDownloadURL() ?>"><?php echo $file->getFileName() ?></a>
                                <?php if (!$message->isConversationMessageDeleted() && $canEditMessage) { ?>
                                    <a rel="<?php echo $attachment['cnvMessageAttachmentID']; ?>"
                                    class="attachment-delete ccm-conversation-message-control-icon ccm-conversation-message-admin-control" href="#"><i class="fa fa-trash-o"></i></a>
                                <?php } ?>
                            </p>
						</div>
    					<?php }
                        $paragraphPadding = '';
                    }
                } ?>
			</div>

			<?php if (!$message->isConversationMessageDeleted() && $message->isConversationMessageApproved()) { ?>
    			<ul>
				<?php if ($enablePosting == Conversation::POSTING_ENABLED && $displayMode == 'threaded') { ?>
					<li><a href="#" data-toggle="conversation-reply" data-post-parent-id="<?=$message->getConversationMessageID()?>"><?=t('Reply')?></a></li>
				<?php } ?>
                <?php if ($enableCommentRating && $canRateMessage) { ?>
                    <li><span class="ccm-conversation-message-divider">|</span></li>
                    <?php
                    $ratingTypes = ConversationRatingType::getList();
                    foreach ($ratingTypes as $ratingType) { ?>
                        <li><?php echo $ratingType->outputRatingTypeHTML(); ?></li>
                    <?php
                    } ?>
                    <li><span class="ccm-conversation-message-rating-score" data-message-rating="<?=$message->getConversationMessageID()?>"><?=$message->getConversationMessageTotalRatingScore(); ?></span></li>
                <?php } ?>
                    <li class="ccm-conversation-social-share"><span class="ccm-conversation-message-divider">|</span></li>
                    <?php if ($displaySocialLinks) { ?>
                        <li class="ccm-conversation-social-share">
                            <a class="ccm-conversation-message-control-icon share-popup" href="https://twitter.com/intent/tweet?url=<?php echo $cnvMessageURL?>"
                            title="<?=t('Share message URL on Twitter.')?>"><i class="fa fa-twitter"></i></a>
                        </li>
                        <li class="ccm-conversation-social-share">
                            <a class="ccm-conversation-message-control-icon share-popup" href="http://www.facebook.com/sharer.php?u=<?php echo $cnvMessageURL?>"
                            title="<?=t('Share message URL on Facebook.')?>"><i class="fa fa-facebook"></i></a>
                        </li>
                    <?php } ?>
                    <li class="ccm-conversation-social-share">
                        <a class="ccm-conversation-message-control-icon share-permalink" data-message-id="<?= $message->getConversationMessageID() ?>" rel="<?php echo $cnvMessageURL ?>"
                        title="<?=t('Get message URL.')?>" data-dialog-title="<?php echo t('Link') ?>"  href="#"><i class="fa fa-link"></i></a>
                    </li>
                </ul>
			<?php } ?>
		</div>
	</div>
<?php
}
