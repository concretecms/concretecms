<?php defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $entity \Concrete\Core\Entity\Express\Entity
 */
$set = $entity->getResultColumnSet();
?>

<div class="ccm-dashboard-header-buttons">
  <?php
  $manage = new \Concrete\Controller\Element\Dashboard\Express\Menu($entity);
  $manage->render();
  ?>
</div>

<div class="pull-right">
  <div class="btn-group">
  <a class="btn btn-primary" href="<?=URL::to('/dashboard/express/create', $entity->getId())?>"><i class="fa fa-plus"></i> <?=t('New %s', $entity->getName())?></a>
  <a class="btn btn-default"  href="<?=URL::to('/dashboard/express/entities', 'view_entity', $entity->getId())?>"><i class="fa fa-cog"></i> <?=t('Settings')?></a>
  </div>
</div>

<div class="spacer-row-6"></div>


<?php

if ($list->getTotalResults()) {
    ?>

<div class="ccm-dashboard-content-full">

  <div class="table-responsive">
    <table class="ccm-search-results-table" data-table="express-entries">
      <thead>
      <tr>
        <?php foreach($set->getColumns() as $column) {
          /** @var $column \Concrete\Core\Search\Column\Column */
          if ($column->isColumnSortable()) { ?>
            <th class="<?=$column->getSortClassName($result)?>"><a href="<?=$column->getSortURL($result)?>"><?php echo $column->getColumnName()?></a></th>
          <?php } else { ?>
            <th><span><?php echo $column->getColumnName()?></span></th>
          <?php } ?>
        <?php } ?>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($results as $o) { ?>
        <tr data-search-row-url="<?=URL::to('/dashboard/express/entries', 'view_entry', $entity->getId(), $o->getId())?>">
        <?php foreach($set->getColumns() as $column) { ?>
          <td><?=$column->getColumnValue($o);?></td>
        <?php } ?>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

</div>

<?php 
} else {
    ?>


    <p><?=t('None created yet.')?></p>


<?php 
} ?>

  </div>