<?php
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Legacy\Loader;
use Concrete\Core\Permission\Key\Key as PermissionKey;
?>

<?php
$pk = PermissionKey::getByID($_REQUEST['pkID']);
$pk->setPermissionObject($calendar);

?>

<?php Loader::element("permission/detail", array('permissionKey' => $pk)); ?>

<script type="text/javascript">
    var ccm_permissionDialogURL = '<?=URL::to("/ccm/calendar/dialogs/permissions", "calendar")?>';
</script>