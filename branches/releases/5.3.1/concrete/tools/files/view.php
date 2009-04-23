<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
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
	die(_("Access Denied."));
}
?>

<div id="ccm-file-manager-download-bar">
<form method="post" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/download/" id="ccm-file-manager-download-form">
<?php echo $form->hidden('fID', $f->getFileID()); ?>
<?php echo $form->hidden('fvID', $f->getFileVersionID()); ?>
<?php echo $form->submit('submit', t('Download'))?>
</form>
</div>

<div style="text-align: center">

<?php 

$to = $fv->getTypeObject();
Loader::element('files/view/' . $to->getView(), array('fv' => $fv));

?>
</div>

<script type="text/javascript">
$(function() {
	$("#ccm-file-manager-download-form").attr('target', ccm_alProcessorTarget);
});
</script>