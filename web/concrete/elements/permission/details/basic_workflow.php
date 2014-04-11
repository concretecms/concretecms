<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<?
$pk = PermissionKey::getByID($_REQUEST['pkID']);
$pk->setPermissionObject($workflow);
?>

<? Loader::element("permission/detail", array('permissionKey' => $pk)); ?>

<script type="text/javascript">
var ccm_permissionDialogURL = '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/basic_workflow?wfID=<?=$workflow->getWorkflowID()?>'; 
</script>