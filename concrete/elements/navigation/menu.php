<?php
defined('C5_EXECUTE') or die('Access Denied.');

if (!empty($top)) {
    ?>
    <ul class="nav flex-column">
        <?php
        $walk = function ($pages) use (&$walk, &$view) {
            $n = count($pages);
            if ($n > 0) {
                for ($i = 0; $i < $n; $i++) {
                    $page = $pages[$i];
                    $next = ($i + 1 < $n) ? $pages[$i + 1] : null;
                    ?>
                    <li class="nav-item">
                        <a class="<?=$view->controller->getMenuItemClass($page)?>" 
                           href="<?=$page->getCollectionLink()?>"><?=t($page->getCollectionName())?></a>
                        <?php
                        if ($view->controller->displayChildPages($page)) {
                            $children = $view->controller->getChildPages($page);
                            if (!empty($children)) {
                                ?>
                                <ul>
                                <?php $walk($children); ?>
                                </ul><?php
                            }
                        }
                        ?>
                    </li>
                    <?php
                }
            }
        };
        $walk($top);
        ?>
    </ul>
    <?php
}
