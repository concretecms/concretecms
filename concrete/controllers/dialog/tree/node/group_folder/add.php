<?php /** @noinspection DuplicatedCode */

namespace Concrete\Controller\Dialog\Tree\Node\GroupFolder;

use Concrete\Controller\Dialog\Tree\Node;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\Request;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Tree\Node\Type\GroupFolder;
use Concrete\Core\User\Group\GroupType;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\JsonResponse;

class Add extends Node
{
    protected $viewPath = '/dialogs/tree/node/group_folder/add';

    protected function canAccess()
    {
        $np = new Checker($this->getNode());
        /** @noinspection PhpUndefinedMethodInspection */
        return $np->canAddGroupFolder();
    }

    public function view()
    {
        $this->set('node', $this->getNode());
        $this->set('allGroupTypes', GroupType::getSelectList());
        $this->set('selectedGroupTypeIds', []);
        $this->set('containsList', [
            GroupFolder::CONTAINS_GROUP_FOLDERS => t("Group Folders"),
            GroupFolder::CONTAINS_GROUP_FOLDERS_AND_GROUPS => t("Group Folders and Groups"),
            GroupFolder::CONTAINS_SPECIFIC_GROUPS => t("Specific Groups")
        ]);
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

        if (!$request->request->has('contains') ||
            !in_array($request->request->get('contains'), [
                GroupFolder::CONTAINS_GROUP_FOLDERS,
                GroupFolder::CONTAINS_GROUP_FOLDERS_AND_GROUPS,
                GroupFolder::CONTAINS_SPECIFIC_GROUPS
            ])) {

            $error->add(t('Invalid value for field contains.'));
        } else {
            $contains = $request->request->get('contains');
        }

        $selectedGroupTypes = [];

        if ($request->request->has('groupTypes') && is_array($request->request->get('groupTypes'))) {
            foreach($request->request->get('groupTypes') as $selectedGroupTypeId) {
                $selectedGroupTypes[] = GroupType::getByID($selectedGroupTypeId);
            }
        }

        if (!$error->has()) {
            /** @noinspection PhpUndefinedVariableInspection */
            $groupFolder = GroupFolder::add($title, $parent, $contains, $selectedGroupTypes);
            $r = $groupFolder->getTreeNodeJSON();
            return new JsonResponse($r);
        } else {
            return new JsonResponse($error);
        }
    }
}
