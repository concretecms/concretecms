<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$u = new User();
$form = Loader::helper('form');

/*
if ($_REQUEST['task'] == 'save_picnik_api_key') {
	Config::save('API_KEY_PICNIK', $_POST['API_KEY_PICNIK']);
}
*/

$f = File::getByID($_REQUEST['fID']);
$fv = $f->getApprovedVersion();

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

$url = $service . '?_apikey=' . $apiKey . '&_export=' . $export . '&' . $valt->getParameter('import_remote') . '&task=update_file&fID=' . $_REQUEST['fID'] . '&_export_field=url_upload_1&_export_agent=browser&_import=' . rawurlencode($image);

?>

<?php 
/*
if ($apiKey == '') { 
	$html = Loader::helper('html');
	print $html->css('ccm.default.theme.css');
	?>

	<h2><?php echo t('A Picnik.com API Key is Required')?></h2>
	
	<ol>
	<li><?php echo t('Obtain an API Key from Picnik.com. <a href="%s">Click here to obtain an API key</a>', 'http://www.picnik.com/keys/login')?></li>
	<li><?php echo t("Enter the API Key below:")?></li>
	</ol>
	
	<form method="post" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/edit/image?task=save_picnik_api_key">
	<?php echo $form->text('API_KEY_PICNIK')?>
	<?php echo $form->hidden('fID')?>
	<?php echo $form->submit('save_picnik', t('Save'))?>
	</form>
	

<?php  } else { */ ?>
	
	<script type="text/javascript">
	parent.jQuery(function() {
		var obj = parent.jQuery('iframe.ccm-file-editor-wrapper').get(0);
		obj.src = '<?php echo $url?>';
	});
	</script>
	

<?php   //} ?>