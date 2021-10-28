<?php

namespace Concrete\Controller\Backend\Dashboard;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class SitemapCheckIn extends AbstractController
{
    public function view(): Response
    {
        $errors = $this->app->make(ErrorList::class);
        $token = $this->app->make(Token::class);
        if (!$token->validate()) {
            $errors->add($token->getErrorMessage());
        } else {
            $page = $this->getPage();
            if ($page === null) {
                $errors->add(t('Unable to find the page specified'));
            } else {
                $page->forceCheckIn();
            }
        }

        $responseFactory = $this->app->make(ResponseFactoryInterface::class);

        return $responseFactory->json($errors->has() ? $errors : true);
    }

    protected function getPageID(): ?int
    {
        $pageID = $this->request->request->get('cID', $this->request->query->get('cID'));

        return $this->app->make(Numbers::class)->integer($pageID, 1) ? (int) $pageID : null;
    }

    protected function getPage(): ?Page
    {
        $pageID = $this->getPageID();
        $page = $pageID === null ? null : Page::getByID($pageID, 'RECENT');

        return $page && !$page->isError() ? $page : null;
    }
}
