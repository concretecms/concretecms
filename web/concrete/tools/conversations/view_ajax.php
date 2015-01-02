<?php defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Conversation\Message\MessageList as ConversationMessageList;
use \Concrete\Core\Conversation\Message\ThreadedList as ConversationMessageThreadedList;

$cnv = Conversation::getByID($_POST['cnvID']);
if (is_object($cnv)) {
	$displayForm = true;
	$enableOrdering = ($_POST['enableOrdering'] == 1) ? true : false;
	$enablePosting = ($_POST['enablePosting'] == 1) ? Conversation::POSTING_ENABLED : Conversation::POSTING_DISABLED_MANUALLY;
	$paginate = ($_POST['paginate'] == 1) ? true : false;
	$enableCommentRating = ($_POST['enableCommentRating']);

    $cp = new Permissions($cnv);
    if (!$cp->canAddConversationMessage()) {
        $enablePosting = Conversation::POSTING_DISABLED_PERMISSIONS;
    }

	if (in_array($_POST['displayMode'], array('flat'))) {
		$displayMode = $_POST['displayMode'];
	} else {
		$displayMode = 'threaded';
	}
	
	$addMessageLabel = t('Add Message');
	if ($_POST['addMessageLabel']) {
		$addMessageLabel = Loader::helper('security')->sanitizeString($_POST['addMessageLabel']);
	}
	switch($_POST['task']) {
		case 'get_messages':
			$displayForm = false;
			break;
	}

	switch($displayMode) {
		case 'flat':
			$ml = new ConversationMessageList();
			$ml->filterByConversation($cnv);
			break;
		default: // threaded
			$ml = new ConversationMessageThreadedList($cnv);
			break;
	}

	switch($_POST['orderBy']) {
		case 'date_desc':
			$ml->sortByDateDescending();
			break;
		case 'date_asc':
			$ml->sortByDateAscending();
			break;
		case 'rating':
			$ml->sortByRating();
			break;
	}

	if ($paginate && Loader::helper('validation/numbers')->integer($_POST['itemsPerPage'])) {
		$ml->setItemsPerPage($_POST['itemsPerPage']);
	} else {
		$ml->setItemsPerPage(-1);
	}

	$summary = $ml->getSummary();
	$totalPages = $summary->pages;
	$args = array(
		'cID' => $_POST['cID'],
		'bID' => $_POST['blockID'],
		'conversation' => $cnv,
		'messages' => $ml->getPage(),
		'displayMode' => $displayMode,
		'displayForm' => $displayForm,
		'enablePosting' => $enablePosting,
		'addMessageLabel' => $addMessageLabel,
		'currentPage' => 1,
		'totalPages' => $totalPages,
		'orderBy' => $_POST['orderBy'],
		'enableOrdering' => $enableOrdering,
		'displayPostingForm' => $_POST['displayPostingForm'],
		'insertNewMessages' => $_POST['insertNewMessages'],
		'enableCommentRating' => $_POST['enableCommentRating'],
		'dateFormat' => $_POST['dateFormat'], 
		'customDateFormat' => $_POST['customDateFormat'],
		'blockAreaHandle' => $_POST['blockAreaHandle'],
        'attachmentsEnabled' => $_POST['attachmentsEnabled'],
        'attachmentOverridesEnabled' => $_POST['attachmentOverridesEnabled']
	);
	Loader::element('conversation/display', $args);
}