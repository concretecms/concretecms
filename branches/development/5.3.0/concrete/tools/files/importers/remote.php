<?

defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/mediabrowser");
$cp = new Permissions($c);
$u = new User();
if (!$cp->canRead()) {
	die(_("Access Denied."));
}

//$valt = Loader::helper('validation/token');
Loader::library("file/importer");
Loader::library('3rdparty/Zend/Http/Client');
Loader::library('3rdparty/Zend/Uri/Http');
Loader::helper('mime');

$errors = array();

// load all the incoming fields into an array
$incomming_urls = array();
for ($i = 1; $i < 6; $i++) {
	$this_url = trim($_POST['url_upload_' .$i]); 

	// did we get anything?
	if (!strlen($this_url))
		continue; 
	
	// validate URL
	if (Zend_Uri_Http::check($this_url)) {
		// URL appears to be good... add it
		$incomming_urls[] = $this_url;
	} else {
		$errors[] = '"' . $this_url . '"' . t(' is not a valid URL.');
	}
}

$import_responses = array();

// if we haven't gotten any errors yet then try to process the form
if (count($errors) < 1) {
	// itterate over each incoming URL adding if relevant
	foreach($incomming_urls as $this_url) {
		// try to D/L the provided file
		$client = new Zend_Http_Client($this_url);
		$response = $client->request();
		
		if ($response->isSuccessful()) {
			$uri = Zend_Uri_Http::fromString($this_url);
			$fname = '';
			$fpath = sys_get_temp_dir() . '/';
	
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
						$fname = date('d-m-Y_H:i_') . mt_rand(100, 999) . '.' . $fextension;
					} while (file_exists($fpath.$fname));
				}
			} //else {
				// if we can't get the filename from the file itself OR from the mime-type I'm not sure there's much else we can do 
			//}
			
			if (strlen($fname)) {
				// write the downloaded file to a temporary location on disk
				$handle = fopen($fpath.$fname, "w");
				fwrite($handle, $response->getBody());
				fclose($handle);
				
				//if ($valt->validate('upload')) {
					// import the file into concrete
					$fi = new FileImporter();
					$resp = $fi->import($fpath.$fname, $fname);
		
					if (!($resp instanceof FileVersion)) {
						switch($resp) {
							case FileImporter::E_FILE_INVALID_EXTENSION:
								$errors[] = t('Invalid file extension.');
								break;
							case FileImporter::E_FILE_INVALID:
								$errors[] = t('Invalid file.');
								break;
						}
					} else {
						$import_responses[] = $resp;
					}
				//} else {
					//$errors[] = $valt->getErrorMessage();
				//}
				
				// clean up the file
				unlink($fpath.$fname);
			} else {
				// could not figure out a file name
				$errors[] = t('Could not determine the name of the file at ') . '"' . $this_url . '".';
			}
		} else {
			// warn that we couldn't download the file
			$errors[] = t('There was an error downloading ') . '"' . $this_url . '".';
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
			alert('<?=implode('\n', $errors)?>');
			window.parent.ccm_alResetSingle();
<? } else { ?>
			highlight = new Array();
<? 	foreach ($import_responses as $r) { ?>
			highlight.push(<?=$r->getFileID()?>);
<?	} ?>
			window.parent.ccm_alRefresh(highlight);
<? } ?>
		</script>
	</head>
	<body>
	</body>
</html>