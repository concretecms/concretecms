<?php

namespace Concrete\Controller\Backend\User;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\Pagination\PaginationFactory;
use Concrete\Core\User\Component\UserSelectInstance;
use Concrete\Core\User\Component\UserSelectInstanceFactory;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\User\UserList;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class Autocomplete extends Controller
{
    /**
     * The maximum number of search results.
     *
     * @var int
     */
    protected const MAX_RESULTS = 7;

    public function checkAccess(): UserSelectInstance
    {
        $instanceFactory = $this->app->make(UserSelectInstanceFactory::class);
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
        $userList = new UserList();
        $userList->filterByFuzzyUserName($query);
        $userList->sortByUserName();
        $userList->setItemsPerPage(static::MAX_RESULTS);
        $factory = new PaginationFactory($this->request);
        $pagination = $factory->createPaginationObject($userList);
        $results = [];
        foreach ($pagination->getCurrentPageResults() as $ui) {
            $results[] = $requestInstance->createResultFromUser($ui);
        }

        return new JsonResponse($results);
    }

    public function getSelectedUsers(): JsonResponse
    {
        $requestInstance = $this->checkAccess();
        $results = [];
        foreach ((array) $this->request->request->get('userId') as $uID) {
            $user = $this->app->make(UserInfoRepository::class)->getByID($uID);
            if ($user) {
                $checker = new Checker($user);
                if (!$checker->canViewUser()) {
                    throw new \Exception(t('Access Denied.'));
                } else {
                    $results[] = $requestInstance->createResultFromUser($user);
                }
            }
        }
        return new JsonResponse($results);
    }


}
