<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<h3><?=t('Express Objects')?></h3>

<?php if (count($entities)) { ?>
<ul class="item-select-list">
<?php foreach ($entities as $entity) {
?>
    <li><a href="<?=$this->action('view', $entity->getID())?>"><i class="fa fa-database"></i> <?=$entity->getName()?></a></li>
<?php
}
?>
</ul>
<?php } else { ?>

    <p><?=t('No entities created yet.')?></p>

<?php }
