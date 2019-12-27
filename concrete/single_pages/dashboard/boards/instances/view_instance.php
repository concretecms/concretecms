<?php

defined('C5_EXECUTE') or die("Access Denied.");

$ui = Core::make(Concrete\Core\Application\Service\UserInterface::class);

$renderer->render($instance);
?>

<div id="ccm-page-controls-wrapper" class="ccm-ui">
    <div id="ccm-toolbar">
        <ul>
            <li class="ccm-logo float-left"><span><?= $ui->getToolbarLogoSRC() ?></span></li>
            <li class="float-left">
                <a href="<?=URL::to('/dashboard/boards/instances', 'view', $instance->getBoard()->getBoardID())?>">
                    <i class="fa fa-arrow-left"></i></span>
                </a>
            </li>
        </ul>
    </div>
</div>
