<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::block('premium_google_map');

//Permissions Check
if( $_GET['bID'] ) { 
		 
	$b = Block::getByID( intval($_GET['bID']) );
	$c = $b->getBlockCollectionObject();
	if(!$b) throw new Exception(t('File not found.'));
	$mapController = new PremiumGoogleMapBlockController($b); 
	$fileId = intval($mapController->getFileID());
	$fileController = LibraryFileBlockController::getFile( $fileId );
	if(!$fileController) throw new Exception(t('File not found.')); 
	 
	header("Pragma: public"); // required
	header("Expires: 0");       
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	header("Content-Type: application/vnd.google-earth.kml+xml kml; charset=utf8");
	header("Content-Disposition: inline; filename=".$fileController->getFilename() ); 
	header("Content-Title: Google Earth KML");
	 
	$cp = new Permissions($c);
	if( $cp->canRead() ) { 

		$filePath=$fileController->getFilePath();
		if( !file_exists($filePath) )
			throw new Exception(t('File not found.'));
	 
		$filesize=filesize($filePath); 
		$fp = fopen($filePath, 'r');
		$file_buffer = fread($fp, $filesize); 
		fclose ($fp);			
		print $file_buffer;
		exit();	 		

	} else { 	
		$v = View::getInstance();
		$v->renderError( t('Permission Denied'),t("You don't have permission to access this file") );
		exit;
	}
			
} else {
	echo t("You don't have permission to access this file");
}
exit;


?>