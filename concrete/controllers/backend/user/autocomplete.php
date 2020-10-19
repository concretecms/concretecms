<?php

namespace Concrete\Controller\Backend\User;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Search\Pagination\PaginationFactory;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserList;
use Concrete\Core\Validation\CSRF\Token;
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

    public function view(): Response
    {
        $this->checkCSRF();
        $result = $this->getResult();

        return $this->app->make(ResponseFactoryInterface::class)->json($result);
    }

    protected function getCSRFAction(): string
    {
        $key = $this->request->request->get('key', $this->request->query->get('key'));

        return 'quick_user_select_' . (is_string($key) ? $key : '');
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkCSRF(): void
    {
        $valt = $this->app->make(Token::class);
        $token = $this->request->request->get('token', $this->request->query->get('token'));
        if (!$valt->validate($this->getCSRFAction(), $token)) {
            throw new UserMessageException($valt->getErrorMessage());
        }
    }

    protected function getSearchTerm(): string
    {
        $term = $this->request->request->get('term', $this->request->query->get('term'));

        return is_string($term) ? $term : '';
    }

    protected function buildResultItem(UserInfo $ui): array
    {
        return [
            'text' => $ui->getUserDisplayName(),
            'value' => $ui->getUserID(),
        ];
    }

    protected function getResult(): array
    {
        $term = $this->getSearchTerm();
        $userList = new UserList();
        $userList->filterByFuzzyUserName($term);
        $userList->sortByUserName();
        $userList->setItemsPerPage(static::MAX_RESULTS);
        $factory = new PaginationFactory($this->request);
        $pagination = $factory->createPaginationObject($userList);
        $result = [];
        foreach ($pagination->getCurrentPageResults() as $ui) {
            $result[] = $this->buildResultItem($ui);
        }

        return $result;
    }
}
