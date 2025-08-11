<?php

namespace Concrete\Controller\Dialog\Groups\Bulk;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Checker;
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
    protected $viewPath = '/dialogs/groups/bulk/delete';

    public function view(): ?Response
    {
        $this->set('form', $this->app->make(Form::class));
        $this->set('groups', $this->resolveGroups());

        return null;
    }

    public function submit(): JsonResponse
    {
        if (!$this->validateAction()) {
            throw new UserMessageException($this->app->make(Token::class)->getErrorMessage());
        }
        $groups = $this->resolveGroups();
        $result = new DeleteGroupCommand\Result();
        $command = new DeleteGroupCommand(0);
        $command
            ->setExtendedResults(true)
            ->setOnChildGroups($this->request->request->getInt('subGroupsOperation'))
            ->setOnlyIfEmpty((bool) $this->request->request->get('onlyIfEmpty'))
        ;
        foreach ($groups as $group) {
            $command->setGroupID($group->getGroupID());
            $result->merge($this->app->executeCommand($command));
        }
        $response = new EditResponse();
        $response->setTitle(t('Groups Deleted'));
        if ($result->getNumberOfDeletedGroups() === 0) {
            $response->setMessage(nl2br(h((string) $result)));
        } else {
            $this->flash('success', (string) $result);
            $response->setReloadCurrentPage(true);
        }

        return $this->app->make(ResponseFactoryInterface::class)->json($response);
    }

    /**
     * @return \Concrete\Core\User\Group\Group[]
     */
    protected function resolveGroups(): array
    {
        $groups = [];
        $items = $this->request('item');
        if (is_array($items)) {
            $sh = $this->app->make(SanitizeService::class);
            $repo = $this->app->make(GroupRepository::class);
            foreach ($items as $uID) {
                $group = $repo->getGroupById($sh->sanitizeInt($uID));
                if ($group !== null && !isset($groups[$group->getGroupID()])) {
                    $gp = new Checker($group);
                    if ($gp->canEditGroup()) {
                        $groups[$group->getGroupID()] = $group;
                    }
                }
            }
        }
        $groups = array_values($groups);
        // Sort groups by path (the deepest ones first)
        usort($groups, static function (Group $a, Group $b): int {
            return count(explode('/', $b->getGroupPath())) - count(explode('/', $a->getGroupPath()));
        });

        return $groups;
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
