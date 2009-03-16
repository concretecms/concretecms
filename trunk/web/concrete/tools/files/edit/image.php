<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$u = new User();
$form = Loader::helper('form');


if ($_REQUEST['task'] == 'save_picnik_api_key') {
	Config::save('API_KEY_PICNIK', $_POST['API_KEY_PICNIK']);
}

$f = File::getByID($_REQUEST['fID']);
$fv = $f->getApprovedVersion();

$fp = new Permissions($f);
if (!$fp->canWrite()) {
	die(t("Access Denied."));
}

$image = BASE_URL . $fv->getRelativePath();
$service = 'http://www.picnik.com/service/';
$export = BASE_URL . REL_DIR_FILES_TOOLS_REQUIRED . '/files/importers/remote';

$valt = Loader::helper('validation/token');

$url = $service . '?_apikey=' . API_KEY_PICNIK . '&_export=' . $export . '&' . $valt->getParameter('import_remote') . '&task=update_file&fID=' . $_REQUEST['fID'] . '&_export_field=url_upload_1&_export_agent=browser&_import=' . rawurlencode($image);

?>

<?
/*
if ($apiKey == '') { 
	$html = Loader::helper('html');
	print $html->css('ccm.default.theme.css');
	?>

	<h2><?=t('A Picnik.com API Key is Required')?></h2>
	
	<ol>
	<li><?=t('Obtain an API Key from Picnik.com. <a href="%s">Click here to obtain an API key</a>', 'http://www.picnik.com/keys/login')?></li>
	<li><?=t("Enter the API Key below:")?></li>
	</ol>
	
	<form method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/edit/image?task=save_picnik_api_key">
	<?=$form->text('API_KEY_PICNIK')?>
	<?=$form->hidden('fID')?>
	<?=$form->submit('save_picnik', t('Save'))?>
	</form>
	

<? } else {  */ ?>
	
	<script type="text/javascript">
	parent.jQuery(function() {
		var obj = parent.jQuery('iframe.ccm-file-editor-wrapper').get(0);
		obj.src = '<?=$url?>';
	});
	</script>
	

<? // } ?>