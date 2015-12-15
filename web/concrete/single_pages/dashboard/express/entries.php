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

<?php

if (count($entries)) { ?>



<?php } else { ?>

    <p><?=t('None created yet.')?></p>

<?php } ?>