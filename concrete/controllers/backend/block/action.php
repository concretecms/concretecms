<?php

namespace Concrete\Controller\Backend\Block;

use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Page\Type\Composer\Control\BlockControl;
use Concrete\Core\Page\Type\Composer\FormLayoutSetControl;
use Concrete\Core\Permission\Checker;
use Symfony\Component\HttpFoundation\Response as SymphonyResponse;

class Action extends AbstractController
{
    /**
     * @param int $cID
     * @param string $arHandle
     * @param int $btID
     * @param string $action
     *
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function add($cID, $arHandle, $btID, $action)
    {
        $c = Page::getByID($cID);
        if (!$c || $c->isError()) {
            throw new UserMessageException(t('Unable to find the specified page'));
        }
        $this->request->setCurrentPage($c);
        $bt = BlockType::getByID($btID);
        if (!$bt) {
            throw new UserMessageException(t('Unable to find the specified block type'));
        }
        $a = Area::getOrCreate($c, $arHandle);
        $ap = new Checker($a);
        $controller = $bt->getController();
        $controller->setAreaObject($a);
        if (!$controller->validateAddBlockPassThruAction($ap, $bt)) {
            throw new UserMessageException(t('Access Denied'));
        }

        return $this->deliverResponse($controller, $action);
    }

    /**
     * @param int $cID
     * @param string $arHandle
     * @param int $bID
     * @param string $action
     *
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit($cID, $arHandle, $bID, $action)
    {
        $c = Page::getByID($cID);
        if (!$c || $c->isError()) {
            throw new UserMessageException(t('Unable to find the specified page'));
        }
        $this->request->setCurrentPage($c);
        $a = Area::getOrCreate($c, $arHandle);
        if ($a->isGlobalArea()) {
            $cx = Stack::getByName($arHandle);
            $ax = Area::get($c, STACKS_AREA_NAME);
        } else {
            $cx = $c;
            $ax = $a;
        }
        $b = Block::getByID($bID, $cx, $ax);
        if (!$b) {
            throw new UserMessageException(t('Unable to find the specified block'));
        }
        $controller = $b->getController();
        if (!$controller->validateEditBlockPassThruAction($b)) {
            throw new UserMessageException(t('Access Denied'));
        }

        return $this->deliverResponse($controller, $action);
    }

    /**
     * @param int $ptComposerFormLayoutSetControlID
     * @param string $action
     *
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function add_composer($ptComposerFormLayoutSetControlID, $action)
    {
        $setControl = FormLayoutSetControl::getByID($ptComposerFormLayoutSetControlID);
        if (!$setControl) {
            throw new UserMessageException(t('Unable to find the specified composer control'));
        }
        $formControl = $setControl->getPageTypeComposerControlObject();
        if (!($formControl instanceof BlockControl)) {
            throw new UserMessageException(t('Unable to find the specified composer control'));
        }
        $set = $setControl->getPageTypeComposerFormLayoutSetObject();
        $type = $set->getPageTypeObject();
        $controller = $formControl->getBlockTypeObject()->getController();
        if (!$controller->validateComposerAddBlockPassThruAction($type)) {
            throw new UserMessageException(t('Access Denied'));
        }

        return $this->deliverResponse($controller, $action);
    }

    /**
     * @param int $cID
     * @param string $arHandle
     * @param int $ptComposerFormLayoutSetControlID
     * @param string $action
     *
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit_composer($cID, $arHandle, $ptComposerFormLayoutSetControlID, $action)
    {
        $c = Page::getByID($cID);
        if (!$c || $c->isError()) {
            throw new UserMessageException(t('Unable to find the specified page'));
        }
        $this->request->setCurrentPage($c);
        $setControl = FormLayoutSetControl::getByID($ptComposerFormLayoutSetControlID);
        if (!$setControl) {
            throw new UserMessageException(t('Unable to find the specified composer control'));
        }
        $formControl = $setControl->getPageTypeComposerControlObject();
        if (!($formControl instanceof BlockControl)) {
            throw new UserMessageException(t('Unable to find the specified composer control'));
        }
        $b = $formControl->getPageTypeComposerControlBlockObject($c);
        if (!is_object($b)) {
            throw new UserMessageException(t('Unable to find the specified block'));
        }
        $controller = $b->getController();
        if (!$controller->validateComposerEditBlockPassThruAction($b)) {
            throw new UserMessageException(t('Access Denied'));
        }

        return $this->deliverResponse($controller, $action);
    }

    /**
     * @param \Concrete\Core\Block\BlockController $controller
     * @param string $action
     *
     * @return string[]
     */
    public function getMethodAndParameters(BlockController $controller, $action)
    {
        $action = trim($action, '/');
        $action = explode('/', $action);

        return $controller->getPassThruActionAndParameters($action);
    }

    /**
     * @param \Concrete\Core\Block\BlockController $controller
     * @param string $action
     *
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function deliverResponse(BlockController $controller, $action)
    {
        list($method, $parameters) = $this->getMethodAndParameters($controller, $action);
        if (!$controller->isValidControllerTask($method, $parameters)) {
            throw new UserMessageException(t('Access Denied'));
        }
        $controller->on_start();
        $response = $controller->runAction($method, $parameters);

        return $response instanceof SymphonyResponse ? $response : new SymphonyResponse($response);
    }
}
