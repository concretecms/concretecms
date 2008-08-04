<?php
require(dirname(__FILE__) . '/../controller.php');
//Permissions Check
if($_GET['bID']) {
	$c = Page::getByID($_GET['cID']);
	$a = Area::get($c, $_GET['arHandle']);
		
	//edit survey mode
	$b = Block::getByID($_GET['bID'],$c, $a);
	
	$bp = new Permissions($b);
	if( $bp->canRead()) {
		$controller = new FileBlockController($b);
		$file = $controller->getFileObject();
		
	
		//$mime_type = finfo_file(DIR_FILES_UPLOADED."/".$filename);
		//header('Content-type: $mime_type');
		// everything else lets just download
		$filename = $file->getFilename();
	
		header('Content-type: application/octet-stream');
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header('Content-Length: ' . filesize(DIR_FILES_UPLOADED."/".$filename));
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header("Content-Transfer-Encoding: binary");
	
		// this should be from FILES -- but lets not break it just yet
		$handle = fopen(DIR_FILES_UPLOADED."/".$filename, "r");
		echo fread($handle, filesize(DIR_FILES_UPLOADED."/".$filename));
		fclose($handle);
	
	} else { 	
		$v = View::getInstance();
		$v->renderError('Permission Denied',"You don't have permission to access this file");
		exit;
	}
			
} else {
	echo "You don't have permission to access this";
}
exit;