<?php
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getCurrentPage();
?>
<div class="col-md-4">
	<div class="list-group">
		<a class="list-group-item <?php if ($c->getCollectionPath() == '/dashboard/system/express/entities') {
    ?>active<?php 
} ?>" href="<?=URL::to('/dashboard/system/express/entities', 'view_entity', $entity->getId())?>"><?=t('Edit Object')?></a>
		<a class="list-group-item <?php if ($c->getCollectionPath() == '/dashboard/system/express/entities/attributes') {
    ?>active<?php 
} ?>" href="<?=URL::to('/dashboard/system/express/entities/attributes', $entity->getId())?>"><?=t('Attributes')?></a>
		<a class="list-group-item <?php if ($c->getCollectionPath() == '/dashboard/system/express/entities/associations') {
    ?>active<?php 
} ?>" href="<?=URL::to('/dashboard/system/express/entities/associations', $entity->getId())?>"><?=t('Associations')?></a>
		<a class="list-group-item <?php if ($c->getCollectionPath() == '/dashboard/system/express/entities/forms') {
    ?>active<?php 
} ?>" href="<?=URL::to('/dashboard/system/express/entities/forms', $entity->getId())?>"><?=t('Forms')?></a>
		<a class="list-group-item <?php if ($c->getCollectionPath() == '/dashboard/system/express/entities/customize_search') {
			?>active<?php
		} ?>" href="<?=URL::to('/dashboard/system/express/entities/customize_search', $entity->getId())?>"><?=t('Customize Search')?></a>
		<a class="list-group-item" href="<?=URL::to('/dashboard/express/entries', $entity->getId())?>"><i class="fa fa-share pull-right" style="margin-top: 4px"></i> <?=t('View %s Entries', $entity->getName())?></a>

	</div>
</div>
