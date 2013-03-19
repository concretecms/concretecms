<?php defined('C5_EXECUTE') or die("Access Denied."); 

//$allowedExts = array("gif", "jpeg", "jpg", "png");
//$extension = end(explode(".", $_FILES["file"]["name"]));
/* if ((($_FILES["file"]["type"] == "image/gif")
|| ($_FILES["file"]["type"] == "image/jpeg")
|| ($_FILES["file"]["type"] == "image/jpg")
|| ($_FILES["file"]["type"] == "image/png"))
&& ($_FILES["file"]["size"] < 20000)
&& in_array($extension, $allowedExts))
  { */
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
    }
  else
    {
    $file = new stdClass();
	move_uploaded_file($_FILES["file"]["tmp_name"],
      "" . $_SERVER['DOCUMENT_ROOT'] . "/files/conversations_pending/" . $_FILES["file"]["name"]);
      Loader::library("file/importer");
		$fi = new FileImporter();
		$fv = $fi->import( $_SERVER['DOCUMENT_ROOT'] . '/files/conversations_pending/' . $_FILES["file"]["name"], $_FILES["file"]["name"]);
		if(!$fv instanceof FileVersion) {
			$file->error = $fi->getErrorMessage($fv);
			echo $file->error;
		} else {
			$file->id 	= $fv->getFileID();
		}
      } echo json_encode($file);
/*  }
else
  {
  echo "Invalid file";
  } */
?>