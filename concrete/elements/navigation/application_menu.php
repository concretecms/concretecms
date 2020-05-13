<?php
defined('C5_EXECUTE') or die('Access Denied.');
use Concrete\Controller\Panel\Dashboard as DashboardPanel;

if (!empty($top)) {
    ?>
    <div class="dashboard-nav-rendered <?php if (isset($wrapperClass) && $wrapperClass) { ?><?=$wrapperClass?><?php } ?>">
        <?php if (isset($title) && $title) {
            ?><h3><?=$title?></h3><?php
        } ?>
        <ul class="nav flex-column">
            <?php
            $n = count($top);
            if ($n > 0) {
              for ($i = 0; $i < $n; $i++) {
                $page = $top[$i];
                $next = ($i + 1 < $n) ? $top[$i + 1] : null;
                $renderTopLevelMenu = $view->controller->displayTopLevelMenu($page);
                $welcomeID = 185;
                $c = Page::getCurrentPage();
                ?>
                <li class="<?=$view->controller->getMenuItemClass($page)?>">
                  <?php if ($renderTopLevelMenu == 1) {?>
                    <a href="<?=$page->getCollectionLink()?>"><?=t($page->getCollectionName())?></a>
                  <?php } else {?>
                    <a href="#" data-launch-sub-panel-url="<?=URL::to('/ccm/system/panels/dashboard/load_menu')?>"
                    data-load-menu="<?=$page->getCollectionId()?>">
                    <?=$page->getCollectionName()?>
                    </a>
                  <?php } ?>
                  <?php
                    $myAccount = 192;
                    $editAccount = 193;
                    $waitingForMe = 186;
                    $waitingForMe = 186;
                    $messages = 195;
                    if (
                      $c->getCollectionID() === $welcomeID ||
                      $c->getCollectionID() === $myAccount ||
                      $c->getCollectionID() === $waitingForMe ||
                      $c->getCollectionID() === $editAccount ||
                      $c->getCollectionID() === $messages
                      ) {
                      if (
                        $page->getCollectionID() === $welcomeID ||
                        $page->getCollectionID() === $myAccount ||
                        $page->getCollectionID() === $waitingForMe ||
                        $page->getCollectionID() === $editAccount ||
                        $page->getCollectionID() === $messages
                      ) {
                        echo '<ul class="welcome-menu">';
                        ?>
                        <li>
                          <a href="<?=URL::to('/dashboard/welcome/me')?>"
                          >
                          <?=t('Waiting For Me')?>
                          </a>
                        </li>
                        <li>
                          <a href="#" data-launch-sub-panel-url="<?=URL::to('/ccm/system/panels/dashboard/load_menu')?>"
                          data-load-menu="<?=$myAccount?>">
                          <?=t('My Account')?>
                          </a>
                        </li>
                        </ul>

                        <?php
                      }
                    }
                  ?>
                </li>
                <?php
                if ($page->getAttribute('is_desktop')) {
                    //echo '<li class="nav-divider"></li>';
                } elseif (is_object($next) && $next->getPackageID() > 0 && $page->getPackageID() == 0) {
                    echo '<li class="nav-divider package-page-divider"></li>';
                }?>
                <?php
              }
            }?>
        </ul>
    </div>
    <?php
}
