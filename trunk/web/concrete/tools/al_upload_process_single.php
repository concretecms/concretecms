<?

defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/mediabrowser");
$cp = new Permissions($c);
$u = new User();
if (!$cp->canRead()) {
	die(_("Access Denied."));
}

require_once(DIR_FILES_BLOCK_TYPES_CORE . '/library_file/controller.php');

if (isset($_FILES['Filedata'])) {
	if(is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
		$bt = BlockType::getByHandle('library_file');
		$data = array();
		$data['file'] = $_FILES['Filedata']['tmp_name'];
		$data['name'] = $_FILES['Filedata']['name'];
		$nb = $bt->add($data);
		$single_upload_success = 1;
	} else {
		$single_upload_success = 0;
	}
	?>
    <html>
    <body>
    <script language="javascript">
		window.parent.ccm_alRefresh();
	</script>
	</body>
    </html>
	<?
} else {
	echo(t('Error: No files sent.'));
}
?>