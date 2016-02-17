<?php defined('C5_EXECUTE') or die("Access Denied.");

if ($entity) {
    /**
     * @var $entity \Concrete\Core\Entity\Express\Entity
     */
    $set = $entity->getResultColumnSet();
    ?>

    <div class="ccm-dashboard-header-buttons">
      <?php
      $manage = new \Concrete\Controller\Element\Dashboard\Express\Menu($entity);
      $manage->enableViewEntityAction();
      $manage->render();
      ?>
    </div>

    <div class="pull-right">
      <div class="btn-group">
      <a class="btn btn-primary" href="<?=URL::to('/dashboard/express/create', $entity->getId())?>"><i class="fa fa-plus"></i> <?=t('New %s', $entity->getName())?></a>
      <a class="btn btn-default"  href="<?=URL::to('/dashboard/system/express/entities', 'view_entity', $entity->getId())?>"><i class="fa fa-cog"></i> <?=t('Settings')?></a>
      </div>
    </div>

    <div class="spacer-row-6"></div>


    <?php

    if ($list->getTotalResults()) {
        ?>

    <div class="ccm-dashboard-content-full">

        <div class="table-responsive">
            <?php View::element('express/entries/search', array('controller' => $searchController)) ?>
        </div>
    </div>

    <?php
    } else {
        ?>


        <p><?=t('None created yet.')?></p>


    <?php
    } ?>

<?php } else { ?>

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


} ?>