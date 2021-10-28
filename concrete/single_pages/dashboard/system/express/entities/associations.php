<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Controller\Element\Dashboard\Express\Menu;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\View\View;

/** @var Entity $entity */

?>

<div class="ccm-dashboard-header-buttons">
    <div class="btn-group">
        <?php
        $manage = new Menu($entity);
        /** @noinspection PhpDeprecationInspection */
        $manage->render();
        ?>

        <a href="<?php echo (string)Url::to('/dashboard/system/express/entities/associations', 'add', $entity->getId()) ?>"
           class="btn btn-primary">
            <?php echo t("Add Association") ?>
        </a>
    </div>
</div>

<div class="row">
    <?php /** @noinspection PhpUnhandledExceptionInspection */
    View::element('dashboard/express/detail_navigation', ['entity' => $entity]) ?>

    <div class="col-md-8">
        <?php if (count($associations)) { ?>
            <ul class="item-select-list" id="ccm-stack-list">
                <?php foreach ($associations as $association) { ?>
                    <?php $formatter = $association->getFormatter(); ?>

                    <li>
                        <a href="<?php echo (string)Url::to('/dashboard/system/express/entities/associations', 'view_association_details', $association->getID()) ?>">
                            <?php echo $formatter->getIcon() ?><?php echo $formatter->getDisplayName() ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <p>
                <?php echo t('You have not created any associations.') ?>
            </p>
        <?php } ?>
    </div>
</div>
