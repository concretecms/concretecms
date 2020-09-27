<?php

defined('C5_EXECUTE') or die('Access Denied.');

 $pk = PermissionKey::getByID($_REQUEST['pkID']);

 View::element('permission/detail', ['permissionKey' => $pk]);

 ?>

<script type="text/javascript">
var ccm_permissionDialogURL = CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/permissions/block_type'; 
</script>