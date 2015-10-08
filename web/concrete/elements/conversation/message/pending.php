<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?
$class = 'ccm-conversation-message ccm-conversation-message-level' . $message->getConversationMessageLevel();
$cnvID = $message->getConversationID();
$cnvMessageID = $message->getConversationMessageID();
?>

<div data-conversation-message-id="<?=$message->getConversationMessageID()?>" data-conversation-message-level="<?=$message->getConversationMessageLevel()?>" class="<?=$class?>">
	<a id="cnv<?=$cnvID?>Message<?=$cnvMessageID?>"></a>
	<div class="ccm-conversation-message-user">
		<div class="ccm-conversation-avatar"><?php print Loader::helper('concrete/avatar')->outputUserAvatar($ui)?></div>
		<div class="ccm-conversation-message-byline">
				<span class="ccm-conversation-message-username"><?
					$author = $message->getConversationMessageAuthorObject();
					$formatter = $author->getFormatter();
					print $formatter->getDisplayName();
					?></span>
			<span class="ccm-conversation-message-divider">|</span>
			<span class="ccm-conversation-message-date"><?=$message->getConversationMessageDateTimeOutput($dateFormat);?></span>
		</div>

	</div>
	<div class="ccm-conversation-message-body">
		<div class="ccm-conversation-message-pending-notice alert alert-info">
			<?=t('This message is pending approval by a moderator.')?>
		</div>
	</div>
</div>
