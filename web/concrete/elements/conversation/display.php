<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<?
if (!is_array($messages)) {
	$messages = array();
}
$u = new User();
$ui = UserInfo::getByID($u->getUserID());
$page = Page::getByID($cID);
$editor = \Concrete\Core\Conversation\Editor\Editor::getActive();
$editor->setConversationObject($args['conversation']);
$val = Loader::helper('validation/token');
$form = Loader::helper('form');
?>

<? Loader::element('conversation/message/add_form', array(
	'blockAreaHandle' => $blockAreaHandle,
	'cID' => $cID,
	'bID' => $bID,
	'editor' => $editor,
	'addMessageLabel' => $addMessageLabel,
	'attachmentsEnabled' => $attachmentsEnabled,
	'displayForm' => $displayForm,
	'displayPostingForm' => $displayPostingForm,
	'position' => 'top',
	'enablePosting' => $enablePosting,
	'conversation' => $conversation
));?>


<div class="ccm-conversation-message-list ccm-conversation-messages-<?=$displayMode?>">

	<div class="ccm-conversation-delete-message" data-dialog-title="<?=t('Delete Message')?>" data-cancel-button-title="<?=t('Cancel')?>" data-confirm-button-title="<?=t('Delete Message')?>">
		<?=t('Remove this message? Replies to it will not be removed.')?>
	</div>
	<div class="ccm-conversation-delete-attachment" data-dialog-title="<?=t('Delete Attachment')?>" data-cancel-button-title="<?=t('Cancel')?>" data-confirm-button-title="<?=t('Delete Attachment')?>">
		<?=t('Remove this attachment?')?>
	</div>
	<div class="ccm-conversation-message-permalink" data-dialog-title="<?=t('Link')?>" data-cancel-button-title="<?=t('Close')?>">
	</div>

	<div class="ccm-conversation-messages-header">
		<? if ($enableOrdering) { ?>
		<select class="form-control pull-right ccm-sort-conversations" data-sort="conversation-message-list">
			<option value="date_asc" <? if ($orderBy == 'date_asc') { ?>selected="selected"<? } ?>><?=t('Earliest First')?></option>
			<option value="date_desc" <? if ($orderBy == 'date_desc') { ?>selected="selected"<? } ?>><?=t('Most Recent First')?></option>
			<option value="rating" <? if ($orderBy == 'rating') { ?>selected="selected"<? } ?>><?=t('Highest Rated')?></option>
		</select>
		<? } ?>

		<? Loader::element('conversation/count_header', array('conversation' => $conversation))?>
	</div>


	<div class="ccm-conversation-no-messages well well-small" <? if (count($messages) > 0) { ?>style="display: none" <? } ?>><?=t('No messages in this conversation.')?></div>

	<div class="ccm-conversation-messages">

	<? foreach($messages as $m) {
		Loader::element('conversation/message', array('cID' => $cID, 'message' => $m, 'bID' => $bID, 'page' => $page, 'blockAreaHandle' => $blockAreaHandle, 'enablePosting' => $enablePosting, 'displayMode' => $displayMode, 'enableCommentRating' => $enableCommentRating, 'dateFormat' => $dateFormat, 'customDateFormat' => $customDateFormat));
	} ?>

	</div>

	<? if ($totalPages > $currentPage) { ?>
	<div class="ccm-conversation-load-more-messages">
		<button class="btn btn-large" type="button" data-load-page="conversation-message-list" data-total-pages="<?=$totalPages?>" data-next-page="<?=$currentPage + 1?>" ><?=t('Load More')?></button>
	</div>
	<? } ?>


</div>

<? Loader::element('conversation/message/add_form', array(
	'blockAreaHandle' => $blockAreaHandle,
	'cID' => $cID,
	'bID' => $bID,
	'editor' => $editor,
	'addMessageLabel' => $addMessageLabel,
	'attachmentsEnabled' => $attachmentsEnabled,
	'displayForm' => $displayForm,
	'displayPostingForm' => $displayPostingForm,
	'position' => 'bottom',
	'enablePosting' => $enablePosting,
	'conversation' => $conversation
));?>