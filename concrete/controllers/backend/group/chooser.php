<?php

namespace Concrete\Controller\Backend\Group;

use Concrete\Core\File\FileList;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\FileTransformer;
use Concrete\Core\File\Set\Set;
use Concrete\Core\Http\Response;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Tree\Type\Group;
use Concrete\Core\User\Group\GroupList;
use Concrete\Core\User\User as UserObject;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use Symfony\Component\HttpFoundation\JsonResponse;

class Chooser extends \Concrete\Core\Controller\Controller
{

    protected function canAccess()
    {
        $permissions = new Checker();
        $token = $this->app->make('token');
        return $permissions->canAccessGroupSearch() && $token->validate();
    }

    public function getTree()
    {
        if ($this->canAccess()) {
            $tree = Group::get();
            return new JsonResponse($tree);
        }
        throw new \Exception(t('Access Denied'));
    }

    public function search()
    {
        if ($this->canAccess()) {
            $list = new GroupList();
            if ($this->request->request('filter') == 'assign') {
                $list->filterByAssignable();
            } else {
                $list->includeAllGroups();
            }
            $keywords = $this->request->request('keywords');
            if (isset($keywords)) {
                $list->filterByKeywords($keywords);
            }
            $list->sortBy('gID', 'asc');
            $results = $list->getResults();
            return new JsonResponse($results);
        }
        throw new \Exception(t('Access Denied'));
    }



}
