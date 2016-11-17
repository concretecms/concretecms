<?php

defined('C5_EXECUTE') or die("Access Denied.");
?>

<i class="fa fa-sliders mobile-leading-icon"></i>
<a href="<?= URL::to('/dashboard') ?>"><?php echo t('Dashboard') ?><i class="fa fa-caret-down"></i></a>
<ul class="list-unstyled">
    <?php
    $walk = function ($pages) use (&$walk, &$view) {
        $n = count($pages);
        if ($n > 0) {
            for ($i = 0; $i < $n; $i++) {
                $page = $pages[$i];
                $next = ($i + 1 < $n) ? $pages[$i + 1] : null;
                ?>
                <li class="<?=$view->controller->getMenuItemClass($page)?>">
                    <a href="<?=$page->getCollectionLink()?>"><?=t($page->getCollectionName())?></a>
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

