<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var $navigation \Concrete\Core\Application\UserInterface\Dashboard\Navigation\Navigation
 */

$walkNavigation = function(array $items) use (&$walkNavigation) {
    if (count($items)) { ?>
        <ul class="nav flex-column">
            <?php foreach($items as $item) { ?>
                <li>
                    <a href="<?=$item->getURL()?>"
                    <?php if ($item->isActive()) { ?>class="ccm-panel-menu-item-active"<?php } ?>
                    <?php if ($item->isActiveParent()) { ?>class="ccm-panel-menu-parent-item-active"<?php } ?>>
                        <?=h($item->getName())?>
                    </a>
                    <?php $walkNavigation($item->getChildren());?>
                </li>
            <?php } ?>
        </ul>
    <?php }
}
?>

<?php $walkNavigation($navigation->getItems());?>