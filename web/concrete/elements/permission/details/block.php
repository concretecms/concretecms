<?
defined('C5_EXECUTE') or die("Access Denied.");
$c = $b->getBlockCollectionObject();
$arHandle = $b->getAreaHandle();
?>

<? $pk = BlockPermissionKey::getByID($_REQUEST['pkID']); ?>
<? $pk->setPermissionObject($b); ?>

<? Loader::element("permission/detail", array('permissionKey' => $pk)); ?>


<script type="text/javascript">
var ccm_permissionDialogURL = '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?bID=<?=$b->getBlockID()?>&arHandle=<?=$b->getAreaHandle()?>&cvID=<?=$c->getVersionID()?>&bID=<?=$b->getBlockID()?>&cID=<?=$c->getCollectionID()?>&btask=set_advanced_permissions'; 
</script>