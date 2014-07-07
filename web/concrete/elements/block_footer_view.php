<?

defined('C5_EXECUTE') or die("Access Denied.");

?>

</div>

<?
if (
    $pt->supportsGridFramework()
    && $b->getBlockAreaObject()->isGridContainerEnabled()
    && !$bt->ignorePageThemeGridFrameworkContainer()
) {
    $gf = $pt->getThemeGridFrameworkObject();
    print '</div>';
    print $gf->getPageThemeGridFrameworkRowEndHTML();
    print $gf->getPageThemeGridFrameworkContainerEndHTML();
}
?>

</div>

<? if ($blockStyle && $blockStyle->getCustomStyleRuleID()) {
    ?>
    </div>
<? } ?>
