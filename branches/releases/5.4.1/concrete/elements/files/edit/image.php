<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
$f = $fv->getFile();
$fp = new Permissions($f);
if (!$fp->canWrite()) {
	die(t("Access Denied."));
}

$apiKey = API_KEY_PICNIK;
//$apiKey = Config::get("API_KEY_PICNIK");
$image = BASE_URL . $fv->getRelativePath();
$service = 'http://www.picnik.com/service/';
$export = BASE_URL . REL_DIR_FILES_TOOLS_REQUIRED . '/files/importers/remote';

$valt = Loader::helper('validation/token');

// $strPicnikUrl is the URL that we use to launch Picnik.
$strPicnikUrl = "http://www.picnik.com/service?".$valt->getParameter('import_remote');

// $aPicnikParams collects together all the params we'll give Picnik.  Start with an API key
$aPicnikParams['_apikey'] = $apiKey;

// tell Picnik where to send the exported image
$aPicnikParams['_export'] = $export;

$aPicnikParams['task'] = "update_file";

$aPicnikParams['fID'] = $f->getFileID();

$aPicnikParams['_export_field'] = "url_upload_1";

$aPicnikParams['_export_agent'] = "browser";

$aPicnikParams['_returntype'] = "text";

$aPicnikParams['_import'] = "image_file";

$aPicnikParams['image_file'] = "@".$fv->getPath();

$ch = curl_init();
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_URL, $strPicnikUrl);
//Don't ask me what this does, I just know that without this funny header, the whole thing doesn't work!
curl_setopt($ch, CURLOPT_HTTPHEADER,array('Expect:'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POST, 1 );

//seems no need to tell it enctype='multipart/data' it already knows
curl_setopt($ch, CURLOPT_POSTFIELDS, $aPicnikParams );

$url = curl_exec( $ch );
curl_close ($ch);

?>
<iframe class="ccm-file-editor-wrapper" id="ccm-file-editor-wrapper<?php echo time()?>" style="padding: 0px; border: 0px; margin: 0px" width="100%" height="100%" frameborder="0" border="0" src="<?php echo $url?>"></iframe>