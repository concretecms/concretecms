<?php defined('C5_EXECUTE') or die("Access Denied."); ?>


<?php
$system = Page::getByPath('/dashboard/system');
$navigation = app(\Concrete\Core\Application\UserInterface\Dashboard\Navigation\FullNavigationFactory::class)
    ->createNavigation();
$modifier = app(\Concrete\Core\Navigation\NavigationModifier::class);
$modifier->addModifier(new \Concrete\Core\Navigation\Modifier\NavigationStartingPointModifier($system));
$navigation = $modifier->process($navigation);
$categories = $navigation->getItems();
?>

<div>
    <?php
    $rowCount = 0;
    for ($i = 0; $i < count($categories); ++$i) {
        /**
         * @var $cat \Concrete\Core\Navigation\Item\ItemInterface
         */
        $cat = $categories[$i];
        if ($rowCount == 3 || $i == 0) {
            $offset = '';
            ?>
            <div class="row">
            <?php
            $rowCount = 0;
        }
        ?>

        <div class="col-md-4 ccm-dashboard-section-menu">
            <h2><?=t($cat->getName())?></h2>


            <ul class="list-unstyled">

                <?php
                $children = $cat->getChildren();
                if (count($children)) {

                    foreach($children as $child) { ?>
                        <li><a href="<?=$child->getUrl()?>"><?=t($child->getName())?></a></li>
                    <?php } ?>

                <?php } else { ?>

                    <li><a href="<?=$cat->getUrl()?>"><?=$cat->getName()?></a></li>

                <?php } ?>

            </ul>

        </div>
        <?php if ($rowCount == 2 || $i == count($categories)) {
            ?>
            </div>
            <?php
        }
        ++$rowCount;
    } ?>
</div>
