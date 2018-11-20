<?php
namespace Concrete\Controller\Dialog\Tree\Node\FileFolder;

use Concrete\Core\Permission\Checker as Permissions;
use Symfony\Component\HttpFoundation\JsonResponse;

class Edit extends Add
{
    protected $viewPath = '/dialogs/tree/node/file_folder/edit';
    protected $helpers = ['form', 'validation/token'];

    protected function canAccess()
    {
        $node = $this->getNode();
        $np = new Permissions($node);

        return $np->canEditTreeNode();
    }

    public function view()
    {
        $node = $this->getNode();
        $this->set('node', $node);
    }

    public function update_file_folder_node()
    {
        $token = $this->app->make('token');
        $error = $this->app->make('error');
        if (!$token->validate('update_file_folder_node')) {
            $error->add($token->getErrorMessage());
        }

        $folderName = $_POST['fileFolderName'];
        if (!is_string($folderName) || trim($folderName) === '') {
            $error->add(t('Invalid folder name'));
        }

        if (!$error->has()) {
            $node = $this->getNode();
            $node->setTreeNodeName($folderName);
            $r = $node->getTreeNodeJSON();

            return new JsonResponse($r);
        } else {
            return new JsonResponse($error);
        }
    }
}
