<?php
defined('C5_EXECUTE') or die('Access Denied.');

$c = Page::getCurrentPage();

if (isset($pageListTitle) && $pageListTitle) {
    ?>
    <div class="ccm-block-page-list-header">
        <<?php echo $titleFormat; ?>><?php echo h($pageListTitle) ?></<?php echo $titleFormat; ?>>
    </div>
    <?php
} ?>

<ul>
<?php foreach ($pages as $page) { ?>

    <li><a href="<?=$page->getCollectionLink()?>"><?=$page->getCollectionName()?></a></li>

<?php } ?>
</ul>

<?php if ($showPagination) { ?>
    <?php echo $pagination; ?>
<?php } ?>

