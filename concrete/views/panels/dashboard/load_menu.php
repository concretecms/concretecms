<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<section id="ccm-panel-dashboard-submenu-<?=$data['parentID']?>">
    <header>
        <a href="" 
          data-panel-navigation="back" 
          class="ccm-panel-back"
          >
            <svg><use xlink:href="#icon-arrow-left" /></svg>
            <span><?=$data['title']?></span>
        </a>

        <h4><?=$data['name']?></h4>
    </header>

    <div class="ccm-panel-content-inner">
     <ul class="nav flex-column ob7-flex-menu">
             
    <?php
			$pages = $data['children'];
      if (!is_array($pages)) {
        $pages = null;
      }
      $n = count($pages);
      if ($n > 0 ) {
        for ($i = 0; $i < $n; $i++) {
          $page = $pages[$i];
          $childPages = $page->getNumChildren();
          $parentMenu = new PageList();
          $parentMenu->filterByExcludeNav(false);
          $parentMenu->sortByDisplayOrder();
          $parentMenu->filterByParentID($page->getCollectionID());
          $parentMenu->includeSystemPages();
          $parentMenu->includeAliases();
          $menuItems = $parentMenu->getResults();
          ?>
          <li>
            <?php if ($childPages > 0 && $menuItems) {?>
            <a href="#"
            data-launch-sub-panel-url="<?= URL::to('/ccm/system/panels/dashboard/load_menu') ?>"
            data-load-menu="<?=$page->getCollectionId()?>"
            data-child-pages="<?=$childPages?>"
            >
              <?=$page->getCollectionName()?>
            </a>

            <?php } else {?>
            <a href="<?=$page->getCollectionLink()?>"
            >
              <?=$page->getCollectionName()?>
            </a>
            <?php }?>
          </li>
          <?php
        }
      }
    ?>
    </ul>

    </div>
</section>
   
