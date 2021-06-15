<?php

namespace Concrete\Controller\Backend\Page;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\PageList;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class Autocomplete extends AbstractController
{
    public function view(): Response
    {
        $this->checkCSRF();
        $pageList = $this->buildPageList();
        $pageNames = $this->getPageNames($pageList);

        return $this->app->make(ResponseFactoryInterface::class)->json($pageNames);
    }

    protected function checkCSRF(): void
    {
        $valt = $this->app->make(Token::class);
        $key = $this->request->request->get('key', $this->request->query->get('key'));
        $token = $this->request->request->get('token', $this->request->query->get('token'));
        if (!$valt->validate("quick_page_select_{$key}", $token)) {
            throw new UserMessageException($valt->getErrorMessage());
        }
    }

    protected function buildPageList(): PageList
    {
        $pageList = new PageList();
        $term = $this->getTerm();
        if ($term !== '') {
            $pageList->filterByName($term);
        }

        return $pageList;
    }

    protected function getTerm(): string
    {
        $term = $this->request->request->get('term');

        return is_string($term) ? $term : '';
    }

    protected function getPageNames(PageList $pageList): array
    {
        $pageNames = [];
        foreach ($pageList->getPagination() as $c) {
            $pageNames[] = [
                'text' => $c->getCollectionName(),
                'value' => $c->getCollectionID(),
            ];
        }

        return $pageNames;
    }
}
