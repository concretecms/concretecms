<?php defined('C5_EXECUTE') or die("Access Denied.");?>

<div class="ccm-dashboard-header-buttons">

    <?php
    $manage = new \Concrete\Controller\Element\Dashboard\Express\Menu($entity);
    $manage->render();
    ?>

</div>


<div class="row">
    <?php View::element('dashboard/express/detail_navigation', array('entity' => $entity))?>
    <div class="col-md-8">
        <h3><?=t('Name')?></h3>
        <p><?=$entity->getName()?></p>

        <h3><?=t('Handle')?></h3>
        <p><?=$entity->getHandle()?></p>

        <h3><?=t('Description')?></h3>
        <p><?=$entity->getDescription()?></p>

    </div>
</div>
