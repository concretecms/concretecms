<?php

defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Conversation\Conversation as Conversation;

$val = Loader::helper('validation/token');
$helperFile = Loader::helper('concrete/file');
$file = new stdClass(); // json return value holder
$error = array();
$pageObj = Page::getByID($_POST['cID']);
$areaObj = Area::get($pageObj, $_POST['blockAreaHandle']);
$blockObj = Block::getByID($_POST['bID'], $pageObj, $areaObj);
$conversation = $blockObj->getController()->getConversationObject();
$config = app('config');

if (!(is_object($conversation))) {
    $error[] = t('Invalid Conversation.');
    $file->error = $error;
    echo Loader::helper('json')->encode($file);
    exit;
}
if ($conversation->getConversationAttachmentOverridesEnabled() > 0) { // check individual conversation for allowing attachments.
    if ($conversation->getConversationAttachmentsEnabled() != 1) {
        $error[] = t('This conversation does not allow file attachments.');
        $file->error = $error;
        echo Loader::helper('json')->encode($file);
        exit;
    }
} elseif (!$config->get('conversations.attachments_enabled')) { // check global config settings for whether or not file attachments should be allowed.
    $error[] = t('This conversation does not allow file attachments.');
    $file->error = $error;
    echo Loader::helper('json')->encode($file);
    exit;
};

$file->timestamp = $_POST['timestamp'];
// --  validation --  //

$tokenValidation = $val->validate('add_conversations_file');

if (!$tokenValidation) {  // check token
    $error[] = t('Bad token');
}

if ($_FILES["file"]["error"] > 0) {  // file errors
    $error[] = $_FILES["file"]["error"];
}

if (!$_POST['bID'] || !$_POST['cID']) {  // bID cID present
    $error[] = t('Block ID or Page ID not sent');
    $errorStr = implode(', ', $error);
    $file->error = $errorStr . '.';
    echo Loader::helper('json')->encode($file);
    exit;
}

if (!is_object($blockObj) || $blockObj->getBlockTypeHandle() != 'core_conversation') { // valid / correct block check
    $error[] = t('Invalid block');
    $errorStr = implode(', ', $error);
    $file->error = $errorStr . '.';
    echo Loader::helper('json')->encode($file);
    exit;
}

$p = new Permissions($blockObj);
if (!$p->canRead()) {    // block read permissions check
    $error[] = t('You do not have permission to view this conversation');
}

// check for registered or guest user file size overrides / limits

$u = Core::make(Concrete\Core\User\User::class);
$blockRegisteredSizeOverride = $conversation->getConversationMaxFileSizeRegistered();
$blockGuestSizeOverride = $conversation->getConversationMaxFilesGuest();
$blockRegisteredQuantityOverride = $conversation->getConversationMaxFilesRegistered();
$blockGuestQuantityOverride = $conversation->getConversationMaxFilesGuest();
$blockExtensionsOverride = $conversation->getConversationFileExtensions();

if ($u->isRegistered()) {
    if ($conversation->getConversationAttachmentOverridesEnabled()) {
        $maxFileSize = $blockRegisteredSizeOverride;
        $maxQuantity = $blockRegisteredQuantityOverride;
    } else {
        $maxFileSize = $config->get('conversations.files.registered.max_size');
        $maxQuantity = $config->get('conversations.files.registered.max');
    }
} else {
    if ($conversation->getConversationAttachmentOverridesEnabled()) {
        $maxFileSize = $blockGuestSizeOverride;
        $maxQuantity = $blockGuestQuantityOverride;
    } else {
        $maxFileSize = $config->get('conversations.files.guest.max_size');
        $maxQuantity = $config->get('conversations.files.guest.max');
    }
}

if ($maxFileSize > 0 && filesize($_FILES["file"]["tmp_name"]) > $maxFileSize * 1000000) {  // max upload size
    $error[] = t('File size exceeds limit');
}

// check file count (this is just for presentation, final count check is done on message submit).
if ($maxQuantity > 0 && ($_POST['fileCount']) > $maxQuantity) {
    $error[] = t('Attachment limit reached');
}

// check filetype extension and overrides

$incomingExtension = strtolower((string) end(explode('.', $_FILES["file"]["name"])));
if ($incomingExtension !== '') {
    $extensionBlackList = $config->get('conversations.files.disallowed_types');
    if ($extensionBlackList === null) {
        $extensionBlackList = $config->get('concrete.upload.extensions_blacklist');
    }
    $validExtension = false;
    $extensionBlackList = array_map('strtolower', $helperFile->unserializeUploadFileExtensions($extensionBlackList));
    if (!in_array($incomingExtension, $extensionBlackList, true)) {
        if ($conversation->getConversationAttachmentOverridesEnabled()) {
            $extensionList = $blockExtensionsOverride;
        } else {
            $extensionList = $config->get('conversations.files.allowed_types');
        }
        $extensionList = array_map('strtolower', $helperFile->unserializeUploadFileExtensions($extensionList));
        if ($extensionList === [] || in_array($incomingExtension, $extensionList, true)) {
            $validExtension = true;
        }
    }
    if ($validExtension !== true) {
        $error[] = t('Invalid File Extension');
    }
}

if (count($error) > 0) {  // send in the errors
    $errorStr = implode(', ', $error);
    $file->error = $errorStr . '.';
    echo Loader::helper('json')->encode($file);
    exit;
}

// -- end intitial validation -- //

// begin file import

$fi = new FileImporter();
$fv = $fi->import($_FILES["file"]["tmp_name"], $_FILES["file"]["name"]);
if (!($fv instanceof \Concrete\Core\Entity\File\Version)) {
    $file->error = $fi->getErrorMessage($fv);
    $file->timestamp = $_POST['timestamp'];
} else {
    $file_set = $config->get('conversations.attachments_pending_file_set');
    $fs = FileSet::getByName($file_set);
    if (!is_object($fs)) {
        $fs = FileSet::createAndGetSet($file_set, FileSet::TYPE_PUBLIC, USER_SUPER_ID);
    }
    $fs->addFileToSet($fv);
    $file->id = $fv->getFileID();
    $file->tag = $_POST['tag'];
    $file->timestamp = $_POST['timestamp'];
}
echo Loader::helper('json')->encode($file);
