<?php
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getCurrentPage();
$board = $instance->getBoard();
?>

<div class="mb-3">
    <a href="<?=URL::to('/dashboard/boards/details', $board->getBoardID())?>">
        <i class="fas fa-arrow-up"></i> <?=t('Back to Board')?>
    </a>
</div>

<div class="list-group">
    <a
            class="list-group-item <?=($action === 'details') ? ' active' : ''?>"
            href="<?=URL::to('/dashboard/boards/instances/details', $instance->getBoardInstanceID())?>"
    >
        <?=t('Details')?>
    </a>
    <a
            class="list-group-item <?=($action === 'rules') ? ' active' : ''?>"
            href="<?=URL::to('/dashboard/boards/instances/rules', $instance->getBoardInstanceID())?>"
    >
        <?=t('Rules')?>
    </a>
</div>
