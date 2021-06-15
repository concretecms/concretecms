<?php

use Concrete\Core\Permission\Key\Key;
use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

$pk = Key::getByID($_REQUEST['pkID'] ?? null);

View::element('permission/detail', ['permissionKey' => $pk]);
?>
<script>
var ccm_permissionDialogURL = CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/permissions/miscellaneous'; 
</script>