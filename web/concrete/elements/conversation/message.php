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
		<? if (!$message->isConversationMessageDeleted() && $message->isConversationMessageApproved()) { ?>
		<ul>
			<li class="ccm-conversation-message-admin-control"><a href="#" data-submit="flag-conversation-message" data-conversation-message-id="<?=$message->getConversationMessageID()?>"><?=t('Flag As Spam')?></a></li>
			<li class="ccm-conversation-message-admin-control"><a href="#" data-submit="delete-conversation-message" data-conversation-message-id="<?=$message->getConversationMessageID()?>"><?=t('Delete')?></a></li>
			
			<? if ($enablePosting && $displayMode == 'threaded') { ?>
				<li><a href="#" data-toggle="conversation-reply" data-post-parent-id="<?=$message->getConversationMessageID()?>"><?=t('Reply')?></a></li>
			<? } ?>
		</ul>
		<? } ?>

		<?=$message->getConversationMessageDateTimeOutput()?> <i class="icon-thumbs-up"></i> <i class="icon-thumbs-down"></i>&nbsp;<span class="ccm-conversation-message-rating">6</span>
	</div>
</div>