<?php defined('C5_EXECUTE') or die('Access Denied.');

if ( !is_object($pkg) ) $pkg = Package::getByHandle($pkg);
if ( !is_object($pkg) ) return;

if ( 0 == count($itemArray) ) return;

?>

<legend><?php echo $pkg->getPackageItemsCategoryDisplayName($key); ?></legend>
<ul>
<?php foreach ($itemArray as $item) { ?>
	<li><?php echo $pkg->getItemName($item); ?></li>
<?php } ?>
</ul>

