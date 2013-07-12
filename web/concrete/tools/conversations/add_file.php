<?php defined('C5_EXECUTE') or die("Access Denied."); 
$val = Loader::helper('validation/token');
$helperFile = Loader::helper('concrete/file');
$file = new stdClass(); // json return value holder
$error = array();
$pageObj = Page::getByID($_POST['cID']);
$areaObj = Area::get($pageObj, $_POST['blockAreaHandle']);
$blockObj = Block::getByID($_POST['bID'], $pageObj, $areaObj);
$file->timestamp = $_POST['timestamp'];
// --  validation --  // 

$tokenValidation = $val->validate('add_conversations_file');

if(!$tokenValidation) {  // check token
	$error[] = t('Bad token');
}

if ($_FILES["file"]["error"] > 0) {  // file errors
	$error[] = $_FILES["file"]["error"];
}

if(!$_POST['bID'] || !$_POST['cID']) {  // bID cID present
	$error[] = t('Block ID or Page ID not sent');
	$errorStr = implode(', ', $error);
	$file->error = $errorStr . '.';
	echo Loader::helper('json')->encode($file);
	exit;
}

if(!is_object($blockObj) || $blockObj->getBlockTypeHandle() != 'core_conversation') { // valid / correct block check
	$error[] = t('Invalid block');
	$errorStr = implode(', ', $error);
	$file->error = $errorStr . '.';
	echo Loader::helper('json')->encode($file);
	exit; 
}

$p = new Permissions($blockObj);
if(!$p->canRead()) {    // block read permissions check
	$error[] = t('You do not have permission to view this conversation');
}

// check for registered or guest user file size overrides / limits

$u = new User();
$blockRegisteredSizeOverride = $blockObj->getController()->maxFileSizeRegistered;
$blockGuestSizeOverride = $blockObj->getController()->maxFileSizeGuest;
$blockRegisteredQuantityOverride = $blockObj->getController()->maxFilesRegistered;
$blockGuestQuantityOverride = $blockObj->getController()->maxFilesGuest;

if ($u->isRegistered()) {
	if($blockRegisteredSizeOverride > 0) { // if block overrides for registered exist, use them instead of global. 
		$maxFileSize = $blockRegisteredSizeOverride;
	} else {
		$maxFileSize = Config::get('CONVERSATIONS_MAX_FILES_REGISTERED');
	}
	
	if($blockRegisteredQuantityOverride > 0) {
		$maxQuantity = $blockRegisteredQuantityOverride;
	} else {
		$maxQuantity = Config::get('CONVERSATIONS_MAX_FILES_REGISTERED');
	}
	 
} else {
	if($blockGuestSizeOverride > 0) {  // if block overrides for guest exist, use them instead of global. 
		 $maxFileSize =  $blockGuestSizeOverride;
	} else {
		$maxFileSize = Config::get('CONVERSATIONS_MAX_FILE_SIZE_GUEST');
	}
	
	if($blockGuestQuantityOverride > 0) {  // if block overrides for guest exist, use them instead of global. 
		 $maxQuantity =  $blockGuestQuantityOverride;
	} else {
		$maxQuantity = Config::get('CONVERSATIONS_MAX_FILES_GUEST');
	}
}


if ($maxFileSize > 0 && filesize($_FILES["file"]["tmp_name"]) > $maxFileSize * 1000000) {  // max upload size
	$error[] = t('File size exceeds limit');
}

// check file count (this is just for presentation, final count check is done on message submit).
if($maxQuantity > 0 && ($_POST['fileCount']) > $maxQuantity) {
	$error[] = t('Attachment limit reached');
}

// check filetype extension and overrides 


$blockExtensionsOverride = $blockObj->getController()->fileExtensions;
if($blockExtensionsOverride) {
	$extensionList = $blockExtensionsOverride;
} else {
	$extensionList = Config::get('CONVERSATIONS_ALLOWED_FILE_TYPES');
}

$extensionList = $helperFile->unserializeUploadFileExtensions($extensionList);

$incomingExtension = end(explode('.', $_FILES["file"]["name"]));
if($incomingExtension && count($extensionList)) {  // check against block file extensions override
	foreach($extensionList as $extension) {
		if(strtolower($extension) == strtolower($incomingExtension)) {
			$validExtension = true;
			break;
		}
	}
	if(!$validExtension) {
		$error[] = t('Invalid File Extension');
	}
}


if(count($error) > 0) {  // send in the errors
	$errorStr = implode(', ', $error);
	$file->error = $errorStr . '.';
	echo Loader::helper('json')->encode($file);
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