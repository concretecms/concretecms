<?

defined('C5_EXECUTE') or die("Access Denied.");

if ($a->isGlobalArea()) {
    $c = Page::getCurrentPage();
    $cID = $c->getCollectionID();
} else {
    $cID = $b->getBlockCollectionID();
    $c = $b->getBlockCollectionObject();
}

$blockStyle = $b->getCustomStyle();
?>

<?
if (
    $pt->supportsGridFramework()
    && $b->getBlockAreaObject()->isGridContainerEnabled()
    && !$b->ignorePageThemeGridFrameworkContainer()
) {
    $gf = $pt->getThemeGridFrameworkObject();
    print '</div>';
    print $gf->getPageThemeGridFrameworkRowEndHTML();
    print $gf->getPageThemeGridFrameworkContainerEndHTML();
}

$p = new Permissions($b);
$showMenu = false;
if ($a->showControls() && $p->canViewEditInterface() && $view->showControls()) { ?>
<? if (is_object($blockStyle)) { ?>
    </div>
<? } ?>
    </div>
    </div>
<? } else if (is_object($blockStyle)) { ?>
    </div>
<? } ?>