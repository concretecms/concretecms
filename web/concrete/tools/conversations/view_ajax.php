<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<? 
$cnv = Conversation::getByID($_POST['cnvID']);
if (is_object($cnv)) {
	$displayForm = true;

	if ($_POST['enablePosting'] == 1) {
		$enablePosting = true;
	} else {
		$enablePosting = false;
	}

	switch($_POST['task']) {
		case 'get_messages':
			$displayForm = false;
			break;
	}

	switch($_POST['orderBy']) {
		case 'date_desc':
			$messages = $cnv->getMessages('date_desc');
			break;
		default:
			$messages = $cnv->getMessages();
			break;
	}


	$args = array(
		'conversation' => $cnv,
		'messages' => $messages,
		'displayForm' => $displayForm,
		'enablePosting' => $enablePosting,
		'orderBy' => $_POST['orderBy']
	);

	Loader::element('conversation/display', $args);
}
