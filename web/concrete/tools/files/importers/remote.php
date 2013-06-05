<?

defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();

$cf = Loader::helper('file');
$fp = FilePermissions::getGlobal();
if (!$fp->canAddFiles()) {
	die(t("Unable to add files."));
}

if (isset($_REQUEST['fID'])) {
	// we are replacing a file
	$fr = File::getByID($_REQUEST['fID']);
} else {
	$fr = false;
}

$searchInstance = $_POST['searchInstance'];

$valt = Loader::helper('validation/token');
Loader::library("file/importer");
Loader::library('3rdparty/Zend/Http/Client');
Loader::library('3rdparty/Zend/Uri/Http');
$file = Loader::helper('file');
Loader::helper('mime');

$error = array();

// load all the incoming fields into an array
$incoming_urls = array();

if (!function_exists('iconv_get_encoding')) {
	$errors[] = t('Remote URL import requires the iconv extension enabled on your server.');
}

if (count($errors) == 0) { 
	for ($i = 1; $i < 6; $i++) {
		$this_url = trim($_REQUEST['url_upload_' .$i]); 
	
		// did we get anything?
		if (!strlen($this_url))
			continue; 
		
		// validate URL
		if (Zend_Uri_Http::check($this_url)) {
			// URL appears to be good... add it
			$incoming_urls[] = $this_url;
		} else {
			$errors[] = Loader::helper('text')->specialchars($this_url) . t(' is not a valid URL.');
		}
	}

	if (!$valt->validate('import_remote')) {
		$errors[] = $valt->getErrorMessage();
	}
				
	
	if (count($incoming_urls) < 1) {
		$errors[] = t('You must specify at least one valid URL.');
	}

}


$import_responses = array();

// if we haven't gotten any errors yet then try to process the form
if (count($errors) < 1) {
	// itterate over each incoming URL adding if relevant
	foreach($incoming_urls as $this_url) {
		// try to D/L the provided file
		$client = new Zend_Http_Client($this_url);
		$response = $client->request();
		
		if ($response->isSuccessful()) {
			$uri = Zend_Uri_Http::fromString($this_url);
			$fname = '';
			$fpath = $file->getTemporaryDirectory();
	
			// figure out a filename based on filename, mimetype, ???
			if (preg_match('/^.+?[\\/]([-\w%]+\.[-\w%]+)$/', $uri->getPath(), $matches)) {
				// got a filename (with extension)... use it
				$fname = $matches[1];
			} else if (! is_null($response->getHeader('Content-Type'))) {
				// use mimetype from http response
				$fextension = MimeHelper::mimeToExtension($response->getHeader('Content-Type'));
				if ($fextension === false)
					$errors[] = t('Unknown mime-type: ') . $response->getHeader('Content-Type');
				else {
					// make sure we're coming up with a unique filename 
					do {
						// make up a filename based on the current date/time, a random int, and the extension from the mime-type
						$fname = date(DATE_APP_FILENAME) . mt_rand(100, 999) . '.' . $fextension;
					} while (file_exists($fpath.'/'.$fname));
				}
			} //else {
				// if we can't get the filename from the file itself OR from the mime-type I'm not sure there's much else we can do 
			//}
			
			if (strlen($fname)) {
				// write the downloaded file to a temporary location on disk
				$handle = fopen($fpath.'/'.$fname, "w");
				fwrite($handle, $response->getBody());
				fclose($handle);
				
				// import the file into concrete
				if ($fp->canAddFileType($cf->getExtension($fname))) {
					$fi = new FileImporter();
					$resp = $fi->import($fpath.'/'.$fname, $fname, $fr);
				} else {
					$resp = FileImporter::E_FILE_INVALID_EXTENSION;
				}
				if (!($resp instanceof FileVersion)) {
					$errors[] .= $fname . ': ' . FileImporter::getErrorMessage($resp) . "\n";
				} else {
					$import_responses[] = $resp;
					
					if (!is_object($fr)) {
						// we check $fr because we don't want to set it if we are replacing an existing file
						$respf = $resp->getFile();
						$respf->setOriginalPage($_POST['ocID']);
					}
					
				}
				
				// clean up the file
				unlink($fpath.'/'.$fname);
			} else {
				// could not figure out a file name
				$errors[] = t('Could not determine the name of the file at ') . $this_url;
			}
		} else {
			// warn that we couldn't download the file
			$errors[] = t('There was an error downloading ') . $this_url;
		}
	}
}
?>
<html>
	<head>
		<script language="javascript">
<? 
if(count($errors)) { 
?>
	window.parent.ccmAlert.notice("<?=t('Upload Error')?>", "<?=str_replace("\n", '', nl2br(implode('\n', $errors)))?>");
	window.parent.ccm_alResetSingle();
<? } else { ?>
		highlight = new Array();
	<? 	foreach ($import_responses as $r) { ?>
			highlight.push(<?=$r->getFileID()?>);
			window.parent.ccm_uploadedFiles.push(<?=intval($r->getFileID())?>);
	<?	} ?>		
		window.parent.jQuery.fn.dialog.closeTop();
		setTimeout(function() { 
			window.parent.ccm_filesUploadedDialog('<?=$searchInstance?>');	
		}, 100);
		
<? } ?>
		</script>
	</head>
	<body>
	</body>
</html>