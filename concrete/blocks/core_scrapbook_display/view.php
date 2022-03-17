<?php defined('C5_EXECUTE') or die('Access Denied.');
/** @var \Concrete\Core\Block\Block $bo */
/** @var int $bOriginalID */
/** @var \Concrete\Core\Block\Block $b */
/** @var \Concrete\Core\Block\View\BlockView $view */
/** @var \Concrete\Block\CoreScrapbookDisplay\Controller $controller */
$area = $b->getBlockAreaObject();
$_bx = \Concrete\Core\Block\Block::getByID($bOriginalID);
if (is_object($_bx)) {
    $_bx->setBlockAreaObject($area);
    $c = \Concrete\Core\Page\Page::getCurrentPage();
    $_bx->setProxyBlock($b);
    $_bx->loadNewCollection($c);
    $_bx->disableBlockContainer();
    $bv = new \Concrete\Core\Block\View\BlockView($_bx);
    $bv->setController($controller->getScrapbookBlockController());
    $bv->disableControls();
    $bv->render('view');
}
