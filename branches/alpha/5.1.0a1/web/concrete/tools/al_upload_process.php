<?
require_once(DIR_FILES_BLOCK_TYPES_CORE . '/library_file/controller.php');
$_FILES['filename'] = $_FILES['Filedata'];

if (isset($_FILES['filename'])) {
	$c = Page::getByPath("/dashboard/mediabrowser");
	$cp = new Permissions($c);
	$u = new User();
	if ($cp->canRead()) {
		$bt = BlockType::getByHandle('library_file');
		$data = array();
		$data['file'] = $_FILES['filename']['tmp_name'];
		$data['name'] = $_FILES['filename']['name'];
		$nb = $bt->add($data);
		echo('OK: '.$data['name']);
	} else {
		echo('ERROR: $cp->canWrite() failed.');
	}
} else {
	echo('ERROR: No files sent.');
}
?>