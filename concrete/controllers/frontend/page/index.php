<?php

namespace Concrete\Controller\Frontend\Page;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class Index extends AbstractController
{
    public function reindexPending(): Response
    {
        $valt = $this->app->make(Token::class);
        if (!$valt->validate()) {
            throw new UserMessageException($valt->getErrorMessage());
        }
        Collection::reindexPendingPages();

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }
}
