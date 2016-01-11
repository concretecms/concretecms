<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

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
        <th><span><?=t('First Name')?></span></th>
        <th><span><?=t('Last Name')?></span></th>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($results as $o) {
    ?>
        <tr data-search-row-url="<?=URL::to('/dashboard/express/entries', 'view_entry', $entity->getId(), $o->getId())?>">
        <td><?php echo $o->getFirstName()?></td>
        <td><?php echo $o->getLastName()?></td>
        </tr>
        <?php 
}
    ?>
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