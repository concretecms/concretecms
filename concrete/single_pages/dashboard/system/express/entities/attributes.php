<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Controller\Element\Attribute\KeyList;
use Concrete\Controller\Element\Dashboard\Express\Menu;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\View\View;

/** @var Entity $entity */
/** @var KeyList $attributeView*/

?>

<?php if (!isset($headerMenu) || !is_object($headerMenu)) { ?>
    <div class="ccm-dashboard-header-buttons">
        <?php
        $manage = new Menu($entity);
        /** @noinspection PhpDeprecationInspection */
        $manage->render();
        ?>
    </div>
<?php } ?>


<div class="row">
    <?php /** @noinspection PhpUnhandledExceptionInspection */
    View::element('dashboard/express/detail_navigation', ['entity' => $entity]) ?>

    <div class="col-md-8">
        <?php
        /** @noinspection PhpDeprecationInspection */
        $attributeView->render();
        ?>
    </div>
</div>
