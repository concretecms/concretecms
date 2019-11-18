<?php
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getCurrentPage();
?>

<div class="list-group">
    <a
        class="list-group-item <?=($c->getCollectionPath() == '/dashboard/boards/details') ? ' active' : ''?>"
        href="<?=URL::to('/dashboard/boards/details', $board->getBoardID())?>"
    >
        <?=t('Details')?>
    </a>
    <a
            class="list-group-item <?=($c->getCollectionPath() == '/dashboard/boards/data_sources') ? ' active' : ''?>"
            href="<?=URL::to('/dashboard/boards/data_sources', $board->getBoardID())?>"
    >
        <?=t('Data Sources')?>
    </a>
</div>
