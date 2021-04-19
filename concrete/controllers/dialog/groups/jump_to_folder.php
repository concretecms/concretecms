<?php

namespace Concrete\Controller\Dialog\Groups;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\User\Group\FolderManager;

class JumpToFolder extends UserInterface
{
    protected $viewPath = '/dialogs/groups/jump_to_folder';

    protected function canAccess()
    {
        $dh = $this->app->make('helper/concrete/user');

        return $dh->canAccessUserSearchInterface();
    }

    public function view()
    {
        $folderManager = new FolderManager();
        $rootTreeNodeID = $folderManager->getRootFolder()->getTreeNodeID();
        $this->set('rootTreeNodeID', $rootTreeNodeID);
    }
}
