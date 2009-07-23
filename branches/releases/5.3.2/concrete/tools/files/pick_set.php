<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/mediabrowser");
$cp = new Permissions($c);
$u = new User();
$form = Loader::helper('form');
if (!$cp->canRead()) {
	die(_("Access Denied."));
}
Loader::model('file_set');

$s1 = FileSet::getMySets();

if ($_POST['task'] == 'pick_set'  && isset($_POST['fsID']{0})) {
	
	$fsID = $_POST['fsID'];
	$fs = FileSet::getByID($fsID);
	foreach ($s1 as $s) {
		if ($s->fsID == $fsID) {
			break;
		}
	}

	exit;
}

$sets = array();
foreach ($s1 as $s){
	$sets[$s->fsID] = $s->fsName;
}

?>

<form method="post" id="ccm-file-pick-set-form" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/pick_set/">
<?php echo $form->hidden('task', 'pick_set')?>
<h1><?php echo t('File Sets')?></h1>

<div class="ccm-file-set-pick-cb">
	<?php echo $form->select('fsID', $sets, isset($_GET['oldFSID']) ? $_GET['oldFSID'] : false)?>
</div>

<br/>
<hr />

<?php 
$h = Loader::helper('concrete/interface');
$b1 = $h->button_js(t('Choose Set'), 'ccm_alSubmitPickSetForm()', 'left');
print $b1;
?>
</form>
