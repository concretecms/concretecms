<?php
defined('C5_EXECUTE') or die("Access Denied.");

if (count($top)) { ?>

<div class="<?php if (isset($wrapperClass) && $wrapperClass) { ?><?=$wrapperClass?><?php } ?>">

<?php if (isset($title) && $title) { ?>
    <h3><?=$title?></h3>
<?php } ?>

<ul class="nav">

<?php

$walk = function ($pages) use (&$walk, &$view, $c) {

    if (count($pages)) {
        for ($i = 0; $i < count($pages); $i++) {
            $page = $pages[$i];
            $next = $pages[$i+1];
            ?>

           <li class="<?=$view->controller->getMenuItemClass($page)?>">


                <a href="<?=$page->getCollectionLink()?>"><?=t($page->getCollectionName())?></a>
                <?php if ($view->controller->displayChildPages($page)) { ?>
                    <?php $children = $view->controller->getChildPages($page);
                    if (count($children)) { ?>
                        <ul>
                        <?php $walk($children, $view, $c); ?>
                        </ul>
                    <?php } ?>
                <?php } ?>

            </li>

            <?php if ($view->controller->displayDivider($page, $next)) { ?>
                <li class="nav-divider"></li>
            <?php } ?>

            <?php
        }
    }
};

$walk($top);

?>
    </ul>
    </div>

    <?php
}