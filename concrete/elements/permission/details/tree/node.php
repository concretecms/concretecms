<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<?php
$pk = PermissionKey::getByID($_REQUEST['pkID']);
$pk->setPermissionObject($node);

?>

<?php Loader::element("permission/detail", array('permissionKey' => $pk)); ?>

<script type="text/javascript">
var ccm_permissionDialogURL = CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/permissions/tree/node?treeNodeID=<?=$node->getTreeNodeID()?>';
</script>