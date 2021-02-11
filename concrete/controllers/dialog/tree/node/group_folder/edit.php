<?php /** @noinspection DuplicatedCode */

namespace Concrete\Controller\Dialog\Tree\Node\GroupFolder;

use Concrete\Controller\Dialog\Tree\Node;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\Request;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\JsonResponse;

class Edit extends Node
{
    protected $viewPath = '/dialogs/tree/node/group_folder/edit';

    protected function canAccess()
    {
        $np = new Checker($this->getNode());
        /** @noinspection PhpUndefinedMethodInspection */
        return $np->canEditTreeNode();
    }

    public function view()
    {
        $this->set('node', $this->getNode());
    }

    public function update_group_folder_node()
    {
        /** @var Token $token */
        $token = $this->app->make(Token::class);
        /** @var Request $request */
        $request = $this->app->make(Request::class);
        $error = new ErrorList();

        $node = $this->getNode();

        if (!$token->validate('update_group_folder_node')) {
            $error->add($token->getErrorMessage());
        }

        if (!$request->request->has('treeNodeGroupFolderName') ||
            strlen($request->request->get('treeNodeGroupFolderName')) === 0) {
            $error->add(t('Invalid title for folder'));
        } else {
            $title = $request->request->get('treeNodeGroupFolderName');
        }

        if (!$error->has()) {
            $node->setTreeNodeName($title);
            $r = $node->getTreeNodeJSON();
            return new JsonResponse($r);
        } else {
            return new JsonResponse($error);
        }
    }
}
