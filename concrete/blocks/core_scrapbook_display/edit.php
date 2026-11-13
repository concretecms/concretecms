<?php
use Concrete\Core\Block\View\BlockView;

defined('C5_EXECUTE') or die('Access Denied.');
/** @var \Concrete\Core\Block\Block $bo */
/** @var int $bOriginalID */
/** @var \Concrete\Core\Block\Block $b */
/** @var BlockView $view */
$bo = \Concrete\Core\Block\Block::getByID($bOriginalID);
if (is_object($bo)) {
    // The original block may not be on any page anymore: check it in the context of the page being edited, like view.php does
    $area = $view->getAreaObject();
    $c = \Concrete\Core\Page\Page::getCurrentPage();
    $bo->setBlockAreaObject($area);
    $bo->setProxyBlock($b);
    $bo->loadNewCollection($c);
    $bp = new \Concrete\Core\Permission\Checker($bo);

    /** @phpstan-ignore-next-line */
    if ($bp->canWrite()) {
        $bv = new BlockView($bo);
        ?>

        <div class="ccm-ui">
            <div class="alert alert-info">
                <?= t('This block was copied from another location. Editing it will create a new instance of it.') ?>
            </div>
        </div>

        <?php

        $bv->setAreaObject($area);
        $bv->addScopeItems($view->getScopeItems());
        $bv->render('edit');
    }
}
