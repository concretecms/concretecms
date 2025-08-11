<?php

declare(strict_types=1);

namespace Concrete\Controller\Dialog\Groups;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Tree\Node\Type\Group as GroupTreeNode;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\Group\CanDeleteGroupsTrait;
use Concrete\Core\User\Group\Command\DeleteGroupCommand;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupRepository;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\Validation\SanitizeService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Delete extends BackendInterfaceController
{
    use CanDeleteGroupsTrait;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/groups/delete';

    public function view(): ?Response
    {
        $this->set('form', $this->app->make(Form::class));
        $this->set('group', $this->resolveGroup());

        return null;
    }

    public function submit(): JsonResponse
    {
        if (!$this->validateAction()) {
            throw new UserMessageException($this->app->make(Token::class)->getErrorMessage());
        }
        $result = new DeleteGroupCommand\Result();
        $group = $this->resolveGroup();
        $parentGroupTreeNodeID = null;
        if ($group !== null) {
            $parentGroup = $group->getParentGroup();
            if ($parentGroup !== null) {
                $parentGroupTreeNode = GroupTreeNode::getTreeNodeByGroupID($parentGroup->getGroupID());
                if ($parentGroupTreeNode !== null) {
                    $parentGroupTreeNodeID = $parentGroupTreeNode->getTreeNodeID();
                }
            }
            $command = new DeleteGroupCommand($group->getGroupID());
            $command
                ->setExtendedResults(true)
                ->setOnChildGroups($this->request->request->getInt('subGroupsOperation'))
                ->setOnlyIfEmpty((bool) $this->request->request->get('onlyIfEmpty'))
            ;
            $result = $this->app->executeCommand($command);
        }
        $response = new EditResponse();
        $response->setTitle(t('Group Deleted'));
        if ($result->getNumberOfDeletedGroups() === 0) {
            $response->setMessage(nl2br(h((string) $result)));
        } else {
            $this->flash('success', (string) $result);
            $urlPaths = ['/dashboard/users/groups'];
            if ($parentGroupTreeNodeID !== null) {
                array_push($urlPaths, 'folder', $parentGroupTreeNodeID);
            }
            $urlResolver = $this->app->make(ResolverManagerInterface::class);
            $response->setRedirectURL((string) $urlResolver->resolve($urlPaths));
        }

        return $this->app->make(ResponseFactoryInterface::class)->json($response);
    }

    protected function resolveGroup(): ?Group
    {
        $sh = $this->app->make(SanitizeService::class);
        $groupID = (int) $sh->sanitizeInt($this->request('groupID'));
        if ($groupID === 0) {
            return null;
        }
        $group = $this->app->make(GroupRepository::class)->getGroupById($groupID);
        if ($group === null) {
            return null;
        }
        $gp = new Checker($group);
        if (!$gp->canEditGroup()) {
            return null;
        }

        return $group;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    protected function canAccess(): bool
    {
        return $this->userCanDeleteGroups();
    }
}
