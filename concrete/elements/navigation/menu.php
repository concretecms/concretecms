<?php
defined('C5_EXECUTE') or die('Access Denied.');

if (!empty($top)) {
    ?>
    <div class="<?php if (isset($wrapperClass) && $wrapperClass) { ?><?=$wrapperClass?><?php } ?>">
        <?php if (isset($title) && $title) {
            ?><h3><?=$title?></h3><?php
        } ?>
        <ul class="nav flex-column">
            <?php
            $walk = function ($pages) use (&$walk, &$view) {
                $n = count($pages);
                if ($n > 0) {
                    for ($i = 0; $i < $n; $i++) {
                        $page = $pages[$i];
                        $next = ($i + 1 < $n) ? $pages[$i + 1] : null;
                        $menuItemClass = $view->controller->getMenuItemClass($page);
                        ?>
                        <li class="nav-item <?=$menuItemClass?>">
                            <a class="nav-link <?php if (strpos($menuItemClass, 'nav-selected') > -1) { ?>active<?php } ?>" href="<?=$page->getCollectionLink()?>"><?=t($page->getCollectionName())?></a>
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
                        if ($page->getAttribute('is_desktop')) {
                            echo '<li class="nav-divider"></li>';
                        } elseif (is_object($next) && $next->getPackageID() > 0 && $page->getPackageID() == 0) {
                            echo '<li class="nav-divider package-page-divider"></li>';
                        }
                    }
                }
            };
            $walk($top);
            ?>
        </ul>
    </div>
    <?php
}
