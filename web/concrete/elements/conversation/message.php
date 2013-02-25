<?
defined('C5_EXECUTE') or die("Access Denied.");
$ui = $message->getConversationMessageUserObject();
?>
<div data-conversation-message-id="<?=$message->getConversationMessageID()?>" data-conversation-message-level="<?=$message->getConversationMessageLevel()?>" class="ccm-conversation-message ccm-conversation-message-level<?=$message->getConversationMessageLevel()?>">
	<div class="ccm-conversation-message-user">
		<div class="ccm-conversation-avatar"><? print Loader::helper('concrete/avatar')->outputUserAvatar($ui)?></div>
		<div class="ccm-conversation-message-byline"><? if (!is_object($ui)) { ?><?=t('Anonymous')?><? } else { ?><?=$ui->getUserDisplayName()?><? } ?></div>
	</div>
	<div class="ccm-conversation-message-body">
		<?=$message->getConversationMessageBodyOutput()?>
	</div>
	<div class="ccm-conversation-message-controls">
		<ul>
			<li class="ccm-conversation-message-admin-control"><a href="#" data-submit="delete-conversation-message" data-conversation-message-id="<?=$message->getConversationMessageID()?>"><?=t('Delete')?></a></li>
			<? if ($enablePosting) { ?>
				<li><a href="#" data-toggle="conversation-reply" data-post-parent-id="<?=$message->getConversationMessageID()?>"><?=t('Reply')?></a></li>
			<? } ?>
		</ul>

		<?=$message->getConversationMessageDateTimeOutput()?>
	</div>
</div>