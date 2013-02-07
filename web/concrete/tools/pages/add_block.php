<?
defined('C5_EXECUTE') or die("Access Denied.");
$ih = Loader::helper('concrete/interface');
?>

<div class="ccm-ui">

<?
$tabs = array();
$sets = BlockTypeSet::getList();
for ($i = 0; $i < count($sets); $i++) {
	$bts = $sets[$i];
	$tabs[] = array($bts->getBlockTypeSetHandle, $bts->getBlockTypeSetName(), $i == 0);
}

print $ih->tabs($tabs);
?>




</div>
