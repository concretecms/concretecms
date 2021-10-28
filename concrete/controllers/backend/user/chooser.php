<?php

namespace Concrete\Controller\Backend\User;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Http\Response;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\User\UserList;
use Concrete\Core\User\UserTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class Chooser extends Controller
{
    /**
     * @var Manager
     */
    protected $manager;

    public function __construct(Manager $manager)
    {
        parent::__construct();

        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\AbstractController::shouldRunControllerTask()
     */
    public function shouldRunControllerTask()
    {
        $tp = $this->app->make('helper/concrete/user');

        return $tp->canAccessUserSearchInterface();
    }

    public function searchUsers($keyword)
    {
        $list = new UserList();
        $list->filterByKeywords($keyword);
        $list->sortByDateAdded();
        $adapter = $list->getPaginationAdapter();
        $pagination = new Pagination($list, $adapter);
        $pagination->setMaxPerPage(20);
        $collection = new Collection($pagination->getCurrentPageResults(), $this->app->make(UserTransformer::class));
        $response = $this->manager->createData($collection);

        return new Response($response->toJson());
    }
}