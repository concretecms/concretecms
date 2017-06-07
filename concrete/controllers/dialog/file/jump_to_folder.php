<?php
namespace Concrete\Controller\Dialog\File;

use Concrete\Controller\Backend\UserInterface\File as BackendInterfaceFileController;
use Concrete\Core\File\EditResponse;
use Concrete\Core\File\Filesystem;
use Concrete\Core\Legacy\FilePermissions;
use Concrete\Core\Tree\Node\Node;
use URL;

class JumpToFolder extends \Concrete\Controller\Backend\UserInterface
{
    protected $viewPath = '/dialogs/file/jump_to_folder';

    protected function canAccess()
    {
        $fp = FilePermissions::getGlobal();
        return $fp->canAccessFileManager();
    }

    public function view()
    {
        $filesystem = new Filesystem();
        $this->requireAsset('core/file-folder-selector');
        $rootTreeNodeID = $filesystem->getRootFolder()->getTreeNodeID();
        $this->set('rootTreeNodeID', $rootTreeNodeID);
    }

}
