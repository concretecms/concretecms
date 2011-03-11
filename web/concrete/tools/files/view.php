<?
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');

$f = File::getByID($_REQUEST['fID']);
if (isset($_REQUEST['fvID'])) {
	$fv = $f->getVersion($_REQUEST['fvID']);
} else {
	$fv = $f->getApprovedVersion();
}

$fp = new Permissions($f);
if (!$fp->canRead()) {
	die(t("Access Denied."));
}
?>

<div id="ccm-file-manager-download-bar">
<form method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/download/" id="ccm-file-manager-download-form">
<?=$form->hidden('fID', $f->getFileID()); ?>
<?=$form->hidden('fvID', $f->getFileVersionID()); ?>
<?=$form->submit('submit', t('Download'))?>
</form>
</div>

<div style="text-align: center">

<?
$to = $fv->getTypeObject();
if ($to->getPackageHandle() != '') {
	Loader::packageElement('files/view/' . $to->getView(), $to->getPackageHandle(), array('fv' => $fv));
} else {
	Loader::element('files/view/' . $to->getView(), array('fv' => $fv));
}
?>
</div>

<script type="text/javascript">
$(function() {
	$("#ccm-file-manager-download-form").attr('target', ccm_alProcessorTarget);
});
</script>