<?php

namespace Concrete\Controller\Backend\Group;

use Concrete\Controller\Backend\Group as GroupController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Tree\Type\Group;
use Concrete\Core\Tree\Node\Type\Group as GroupNode;
use Concrete\Core\User\Group\GroupList;

class Chooser extends GroupController
{
    /**
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getTree()
    {
        $this->checkAccess(true);
        $tree = Group::get();

        $guestGroupNode = GroupNode::getTreeNodeByGroupID(GUEST_GROUP_ID);
        $registeredGroupNode = GroupNode::getTreeNodeByGroupID(REGISTERED_GROUP_ID);

        return $this->app->make(ResponseFactoryInterface::class)->json([
            'tree' => $tree,
            'guestGroupTreeNodeID' => $guestGroupNode->getTreeNodeID(),
            'registeredGroupTreeNodeID' => $registeredGroupNode->getTreeNodeID(),
        ]);
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function search()
    {
        $this->checkAccess(true);
        $list = new GroupList();
        if ($this->request->request('filter') === 'assign') {
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

        return $this->app->make(ResponseFactoryInterface::class)->json($results);
    }

    /**
     * @deprecated use the checkAccess() method
     * @see \Concrete\Controller\Backend\Group::checkAccess()
     *
     * @return bool
     */
    protected function canAccess()
    {
        try {
            $this->checkAccess(true);

            return true;
        } catch (UserMessageException $x) {
            return false;
        }
    }
}
