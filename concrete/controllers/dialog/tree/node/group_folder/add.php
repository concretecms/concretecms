<?php /** @noinspection DuplicatedCode */

namespace Concrete\Controller\Dialog\Tree\Node\GroupFolder;

use Concrete\Controller\Dialog\Tree\Node;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\Request;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Tree\Node\Type\GroupFolder;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\JsonResponse;

class Add extends Node
{
    protected $viewPath = '/dialogs/tree/node/group_folder/add';

    protected function canAccess()
    {
        $np = new Checker($this->getNode());
        /** @noinspection PhpUndefinedMethodInspection */
        return $np->canAddTreeNode();
    }

    public function view()
    {
        $this->set('node', $this->getNode());
    }

    public function add_group_folder_node()
    {
        /** @var Token $token */
        $token = $this->app->make(Token::class);
        /** @var Request $request */
        $request = $this->app->make(Request::class);
        $error = new ErrorList();

        $parent = $this->getNode();

        if (!$token->validate('add_group_folder_node')) {
            $error->add($token->getErrorMessage());
        }

        if (!$request->request->has('treeNodeGroupFolderName') ||
            strlen($request->request->get('treeNodeGroupFolderName')) === 0) {
            $error->add(t('Invalid title for folder'));
        } else {
            $title = $request->request->get('treeNodeGroupFolderName');
        }

        if (!$error->has()) {
            /** @noinspection PhpUndefinedVariableInspection */
            $groupFolder = GroupFolder::add($title, $parent);
            $r = $groupFolder->getTreeNodeJSON();
            return new JsonResponse($r);
        } else {
            return new JsonResponse($error);
        }
    }
}
