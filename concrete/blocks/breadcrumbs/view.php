<?php defined('C5_EXECUTE') or die('Access Denied.');
/** @var \Concrete\Core\Navigation\Breadcrumb\PageBreadcrumb|null $breadcrumb */
$breadcrumb = $breadcrumb ?? null;
if ($breadcrumb && count($breadcrumb->getItems()) > 0) {
    ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <?php
            /** @var \Concrete\Core\Navigation\Item\Item $item */
            foreach ($breadcrumb->getItems() as $item) {
                if ($item->isActive()) {
                    ?>
                    <li class="breadcrumb-item active" aria-current="page"><?= h($item->getName()) ?></li>
                    <?php
                } else {
                    ?>
                    <li class="breadcrumb-item"><a href="<?= h($item->getUrl()) ?>"><?= h($item->getName()) ?></a></li>
                    <?php
                }
            }
            ?>
        </ol>
    </nav>
    <?php
}
