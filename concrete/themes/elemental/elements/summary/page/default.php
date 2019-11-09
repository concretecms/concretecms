<?php defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $page Concrete\Core\Page\
 */
?>

<div>
    <a href="<?=$page->getCollectionLink()?>"><?=$page->getCollectionName()?></a>
    <?php if ($page->getCollectionDescription()) { ?>
        <div><?=$page->getCollectionDescription()?></div>
    <?php } ?>
</div>
