<?php defined('C5_EXECUTE') or die("Access Denied."); 
$val = Loader::helper('validation/token');
$vs = Loader::helper('validation/strings');
$ve = Loader::helper('validation/error');
$file = new stdClass(); // json return value holder
$file->timestamp = $_POST['timestamp'];
// --  validation --  // 

$tokenValidation = $val->validate('add_conversations_file');

if(!$tokenValidation) {  // check token
	$file->error[] = t('Bad token');
}

if ($_FILES["file"]["error"] > 0) {  // file errors
	$file->error[] = $_FILES["file"]["error"];
}

if(!$_POST['bID'] || !$_POST['cID']) {  // bID cID present
	$file->error[] = t('Block ID or Page ID not sent.');
}

$blockObj = Block::getByID($_POST['bID'], Page::getByID($_POST['cID']), $_POST['blockAreaHandle']);

if(!is_object($blockObj) || $blockObj->getBlockTypeHandle() != 'core_conversation') { // valid / correct block check
	$file->error[] = t('Invalid block.');
}

$p = new Permissions($blockObj);
if(!$p->canRead()) {    // block read permissions check
	$file->error[] = t('You do not have permission to view this conversation.');
}

// check for registered or guest user file size overrides / limits

$u = new User();
$blockRegisteredOverride = $blockObj->getController()->maxFileSizeRegistered;
$blockGuestOverride = $blockObj->getController()->maxFileSizeGuest;

if ($u->isRegistered()) {
	if($blockRegisteredOverride > 0) { // if block overrides for registered exist, use them instead of global. 
		$maxFileSize = $blockRegisteredOverride;
	} else {
		// use system defaults
	}
	
} else {
	if($blockGuestOverride) {  // if block overrides for guest exist, use them instead of global. 
		 $maxFileSize =  $blockGuestOverride;
	} else {
		// use system defaults 
	}
}

// check filetype extension and overrides 


$blockExtensionsOverride = $blockObj->getController()->fileExtensions;
if($blockExtensionsOverride) {
	$extensionList = $blockExtensionsOverride;
} else {
	// get system defaults
}

$incomingExtension = end(explode('.', $_FILES["file"]["name"]));
if($incomingExtension && strlen($blockExtensionsOverride)) {  // check against block file extensions override
	foreach(explode(',', $blockExtensionsOverride) as $overrideExtension) {
		if($overrideExtension == $incomingExtension) {
			$validExtension = true;
			break;
		}
	}
	if(!$validExtension) {
		$file->error[] = t('Invalid File Extension');
	}
}

// get block level file size and types if they exist
$maxFileSizeGuest =  $blockObj->getController()->maxFileSizeGuest;
$maxFileSizeRegistered =  $blockObj->getController()->maxFileSizeRegistered;

// otherwise get global file size, types, and quantity settings

if ($maxFileSizeGuest > 0 && filesize($_FILES["file"]["tmp_name"]) > $maxFileSizeGuest * 1000000) {  // max upload size
	$file->error[] = t('File exceeds size limit.');
}

if(is_array($file->error)) {
	Loader::helper('json')->encode($file);
	exit;
}

// -- end intitial validation -- // 


// begin file import

move_uploaded_file($_FILES["file"]["tmp_name"],
$_SERVER['DOCUMENT_ROOT'] . "/files/tmp/" . $_FILES["file"]["name"]);
Loader::library("file/importer");
$fi = new FileImporter();
$fv = $fi->import( $_SERVER['DOCUMENT_ROOT'] . '/files/tmp/' . $_FILES["file"]["name"], $_FILES["file"]["name"]);
unlink($_SERVER['DOCUMENT_ROOT'] . '/files/tmp/' . $_FILES["file"]["name"]);
if(!$fv instanceof FileVersion) {
	$file->error = $fi->getErrorMessage($fv);
	$file->timestamp = $_POST['timestamp'];
} else {
	$fs = FileSet::getByName(CONVERSATION_MESSAGE_ATTACHMENTS_PENDING_FILE_SET);
	if (!is_object($fs)) {
		$fs = FileSet::createAndGetSet(CONVERSATION_MESSAGE_ATTACHMENTS_PENDING_FILE_SET, FileSet::TYPE_PUBLIC, USER_SUPER_ID);
	}
	$fs->addFileToSet($fv);
	$file->id 	= $fv->getFileID();
	$file->tag = $_POST['tag'];
	$file->timestamp = $_POST['timestamp'];
	}
echo Loader::helper('json')->encode($file);
?>