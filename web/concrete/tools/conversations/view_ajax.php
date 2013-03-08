<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<? 
$cnv = Conversation::getByID($_POST['cnvID']);
if (is_object($cnv)) {
	$displayForm = true;
	$enableOrdering = ($_POST['enableOrdering'] == 1) ? true : false;
	$enablePosting = ($_POST['enablePosting'] == 1) ? true : false;
	$paginate = ($_POST['paginate'] == 1) ? true : false;

	if (in_array($_POST['displayMode'], array('flat'))) {
		$displayMode = $_POST['displayMode'];
	} else {
		$displayMode = 'threaded';
	}
	
	switch($_POST['task']) {
		case 'get_messages':
			$displayForm = false;
			break;
	}

	switch($displayMode) {
		case 'flat':
			$ml = new ConversationMessageList($cnv);
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
		'conversation' => $cnv,
		'messages' => $ml->getPage(),
		'displayMode' => $displayMode,
		'displayForm' => $displayForm,
		'enablePosting' => $enablePosting,
		'currentPage' => 1,
		'totalPages' => $totalPages,
		'orderBy' => $_POST['orderBy'],
		'enableOrdering' => $enableOrdering
	);

	Loader::element('conversation/display', $args);
}