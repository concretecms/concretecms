<?php

namespace Concrete\Core\Block\Traits;

use Concrete\Core\Block\Block;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\ErrorList\Formatter\JsonFormatter;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * A port of \Concrete\Controller\Dialog\Block::validateBlock functionality that can be
 * more easily included in different situations
 */
trait ValidateBlockRequestTrait
{

    public function validateBlock(Block $b, array $postBody): ?JsonResponse
    {
        $bi = $b->getController();
        if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
            $_b = Block::getByID($bi->getOriginalBlockID());
            $bi = $_b->getController(); // for validation
        }

        $e = $bi->validate($postBody);
        if ($e === true) {
            $e = null;
        }
        if ($e instanceof ErrorList && $e->has()) {
            $formatter = new JsonFormatter($e);
            return new JsonResponse($formatter->asArray());
        }
        return null;
    }


}
