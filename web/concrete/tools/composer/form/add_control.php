<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
$c = Page::getByPath('/dashboard/composer/list/form');
$cp = new Permissions($c);
$ih = Loader::helper('concrete/interface');
if ($cp->canViewPage()) { ?>

	<div class="ccm-ui">
	<?
	$tabs = array();
	$types = ComposerControlType::getList();
	for ($i = 0; $i < count($types); $i++) {
		$type = $types[$i];
		$tabs[] = array($type->getComposerControlTypeHandle(), $type->getComposerControlTypeName(), $i == 0);
	}

	print $ih->tabs($tabs);

	foreach($types as $t) { ?>

	<div class="ccm-tab-content" id="ccm-tab-content-<?=$t->getComposerControlTypeHandle()?>">
	<ul class="item-select-list">
		<? 
		$controls = $t->getComposerControlObjects();
		foreach($controls as $cnt) { ?>
			<li><a href="#" data-control-type-id="<?=$t->getComposerControlTypeID()?>" data-control-identifier="<?=$cnt->getComposerControlIdentifier()?>" style="background-image: url('<?=$cnt->getComposerControlIconSRC()?>')"><?=$cnt->getComposerControlName()?></a></li>
		<? } ?>
	</ul>
	</div>


	<? } ?>

	</div>

<style type="text/css">
	ul.item-select-list li a {
		background-size: 16px 16px;
	}
</style>



<?

}