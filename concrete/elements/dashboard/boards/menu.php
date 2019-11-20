<?php
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getCurrentPage();
?>

<div class="list-group">
    <a
        class="list-group-item <?=($action == 'details') ? ' active' : ''?>"
        href="<?=URL::to('/dashboard/boards/details', $board->getBoardID())?>"
    >
        <?=t('Details')?>
    </a>
    <a
            class="list-group-item <?=($action == 'data_sources') ? ' active' : ''?>"
            href="<?=URL::to('/dashboard/boards/data_sources', $board->getBoardID())?>"
    >
        <?=t('Data Sources')?>
    </a>
    <a
            class="list-group-item <?=($action == 'rebuild') ? ' active' : ''?>"
            href="<?=URL::to('/dashboard/boards/rebuild', $board->getBoardID())?>"
    >
        <?=t('Rebuild Board')?>
    </a>
</div>
