<?php

namespace Concrete\Controller\Backend\Page;

use Concrete\Controller\Backend\Page;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Component\PageSelectInstance;
use Concrete\Core\Page\Component\PageSelectInstanceFactory;
use Concrete\Core\Page\PageList;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\Pagination\PaginationFactory;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Concrete\Core\Page\Page as CorePage;

defined('C5_EXECUTE') or die('Access Denied.');

class Autocomplete extends AbstractController
{
    public function checkAccess(): PageSelectInstance
    {
        $instanceFactory = $this->app->make(PageSelectInstanceFactory::class);
        $requestInstance = $instanceFactory->createInstanceFromRequest($this->request);

        if (!$instanceFactory->instanceMatchesAccessToken($requestInstance, $this->request->request->get('accessToken') ?? '')) {
            throw new UserMessageException($this->app->make('token')->getErrorMessage());
        }

        return $requestInstance;
    }

    public function view(): Response
    {
        $requestInstance = $this->checkAccess();
        $query = $this->request->request->get('query', $this->request->query->get('query'));
        $pageList = new PageList();
        $pageList->filterByName($query);
        $factory = new PaginationFactory($this->request);
        $pagination = $factory->createPaginationObject($pageList);
        $results = [];
        foreach ($pagination->getCurrentPageResults() as $c) {
            $results[] = $requestInstance->createResultFromPage($c);
        }

        return new JsonResponse($results);
    }

    public function getSelectedPages(): JsonResponse
    {
        $requestInstance = $this->checkAccess();
        $results = [];
        foreach ((array) $this->request->request->get('pageId') as $cID) {
            $page = CorePage::getByID($cID, 'ACTIVE');
            $permissions = new Checker($page);
            if (!$permissions->canViewPage()) {
                throw new \Exception(t('Access Denied.'));
            } else {
                $results[] = $requestInstance->createResultFromPage($page);
            }
        }
        return new JsonResponse($results);
    }

}
