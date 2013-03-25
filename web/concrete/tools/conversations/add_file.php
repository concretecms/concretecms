<?php defined('C5_EXECUTE') or die("Access Denied."); 

  /*   converations permissions here -- realpath ? */ 
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
    }
  else
    {
    $file = new stdClass();
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
      } echo Loader::helper('json')->encode($file);

?>