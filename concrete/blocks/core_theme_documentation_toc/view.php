<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @param \Concrete\Core\Navigation\Item\ItemInterface[] $items
 * @param int $level
 */
$walkNavigation = static function (array $items, int $level) use (&$walkNavigation) {
    if (count($items)) { ?>
        <ul class="<?php if ($level > 1) { ?>ps-4<?php } ?> nav flex-column">
            <?php foreach($items as $item) { ?>
                <li class="nav-item">
                    <a target="_top" class="nav-link <?php if (count($item->getChildren())) { ?>disabled fw-bold<?php } ?>" href="<?=$item->getURL()?>">
                        <?=h($item->getName())?>
                    </a>
                    <?php $walkNavigation($item->getChildren(), $level + 1); ?>
                </li>
            <?php } ?>
        </ul>
    <?php }
}
?>

<?php
/** @var \Concrete\Core\Navigation\Navigation|null $navigation */
 if ($navigation) {
     ?>
     <div class="p-4 bg-light">
         <?php $walkNavigation($navigation->getItems(), 1); ?>
     </div>
     <?php
 }
?>
