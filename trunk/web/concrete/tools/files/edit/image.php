<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/mediabrowser");
$cp = new Permissions($c);
$u = new User();
$form = Loader::helper('form');
if (!$cp->canRead()) {
	die(_("Access Denied."));
}

if ($_REQUEST['task'] == 'save_picnik_api_key') {
	Config::save('API_KEY_PICNIK', $_POST['API_KEY_PICNIK']);
}

$f = File::getByID($_REQUEST['fID']);
$fv = $f->getApprovedVersion();

$image = BASE_URL . $fv->getRelativePath();
$apiKey = 'be63e3b4ae4f0a0035caf17fc5f2f02b';
$service = 'http://www.picnik.com/service/';
$export = BASE_URL . REL_DIR_FILES_TOOLS_REQUIRED . '/files/edit?fID=' . $fv->getFileID();
$apiKey = Config::get('API_KEY_PICNIK');

$url = $service . '?_apikey=' . $apiKey . '&_export=' . rawurlencode($export) . '&_export_method=POST&export_agent=browser&_import=' . rawurlencode($image);

?>

<? if ($apiKey == '') { 
	$form = Loader::helper('form');
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
	<?=$form->submit('save_picnik', t('Save'))?>
	</form>
	

<? } else { ?>
	
	<script type="text/javascript">
	parent.jQuery(function() {
		var obj = parent.jQuery('iframe.ccm-file-editor-wrapper').get(0);
		obj.src = '<?=$url?>';
	});
	</script>
	

<? } ?>