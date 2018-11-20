<?php
namespace Concrete\Controller\Dialog\Tree\Node\FileFolder;

use Concrete\Controller\Dialog\Tree\Node;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\File\Filesystem;
use Concrete\Core\Permission\Checker as Permissions;
use Concrete\Core\Tree\Node\Node as TreeNode;
use Symfony\Component\HttpFoundation\JsonResponse;

class Add extends Node
{
    protected $viewPath = '/dialogs/tree/node/file_folder/add';
    protected $helpers = ['form', 'validation/token'];

    protected function getNode()
    {
        if (!isset($this->node)) {
            $filesystem = new Filesystem();
            $treeNodeID = $this->app->make('helper/security')->sanitizeInt($this->request->query->get('treeNodeID'));
            if (empty($treeNodeID)) {
                $treeNodeID = $this->app->make('helper/security')->sanitizeInt($this->request->request->get('treeNodeID'));
            }
            if ($treeNodeID) {
                $node = TreeNode::getByID($treeNodeID);
                if (is_object($node) && $node instanceof \Concrete\Core\Tree\Node\Type\FileFolder) {
                    $this->node = $node;
                }
            }

            if (!isset($this->node)) {
                $this->node = $filesystem->getRootFolder();
            }
        }

        return $this->node;
    }

    protected function canAccess()
    {
        $node = $this->getNode();
        $np = new Permissions($node);

        return $np->canAddTreeSubNode();
    }

    public function add_file_folder_node()
    {
        $token = $this->app->make('token');
        $error = $this->app->make('error');
        $response = new EditResponse();
        $response->setError($error);

        if (!$token->validate('add_file_folder_node')) {
            $error->add($token->getErrorMessage());
        }

        $folderName = $this->request->request->get('fileFolderName');
        if (!is_string($folderName) || trim($folderName) === '') {
            $error->add(t('Folder Name can not be empty.'));
        }

        if (!$error->has()) {
            $filesystem = new Filesystem();
            $folder = $filesystem->addFolder($this->node, $folderName);
            $response->setMessage(t('Folder added.'));
            $response->setAdditionalDataAttribute('folder', $folder);

            return new JsonResponse($response);
        } else {
            return new JsonResponse($error);
        }
    }
}
