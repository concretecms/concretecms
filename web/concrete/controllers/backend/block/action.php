<?php
namespace Concrete\Controller\Backend\Block;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Http\Response;

class Action extends AbstractController
{

    public function add($cID, $arHandle, $btID, $action)
    {
        $c = \Page::getByID($cID);
        if (is_object($c) && !$c->isError()) {
            $a = \Area::getOrCreate($c, $arHandle);
            if (is_object($a)) {
                $ap = new \Permissions($a);
                $bt = \BlockType::getByID($btID);
                if (is_object($bt) && $ap->canAddBlock($bt)) {
                    $controller = $bt->getController();
                    return $this->deliverResponse($controller, $action);
                }
            }
        }

        $response = new Response(t('Access Denied'));
        return $response;
    }

    protected function deliverResponse(BlockController $controller, $action)
    {
        list($method, $parameters) = $controller->getPassThruActionAndParameters(array($action));
        if ($controller->isValidControllerTask($method, $parameters)) {
            $controller->on_start();
            $response = $controller->runAction($method, $parameters);
            if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
                return $response;
            } else {
                $r = new Response($response);
                return $r;
            }
        }
        $response = new Response(t('Access Denied'));
        return $response;
    }

    public function edit($cID, $arHandle, $bID, $action)
    {
        $c = \Page::getByID($cID);
        if (is_object($c) && !$c->isError()) {
            $b = \Block::getByID($bID, $c, $arHandle);
            if (is_object($b)) {
                $bp = new \Permissions($b);
                if ($bp->canEditBlock()) {
                    $controller = $b->getController();
                    return $this->deliverResponse($controller, $action);
                }
            }
        }

        $response = new Response(t('Access Denied'));
        return $response;
    }
}
