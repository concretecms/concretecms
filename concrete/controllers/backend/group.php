<?php

namespace Concrete\Controller\Backend;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\Group\EditResponse;
use Concrete\Core\User\Group\GroupRepository;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\JsonResponse;

class Group extends Controller
{
    public function getJSON(): JsonResponse
    {
        $response = new EditResponse();
        try {
            $this->checkAccess(false);
            $response->setGroups($this->getRequestGroups($response->getError()));
        } catch (UserMessageException $x) {
            $response->getError()->addError($x);
        }

        return $this->app->make(ResponseFactoryInterface::class)->json($response);
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkAccess(bool $checkToken): void
    {
        if ($checkToken) {
            $token = $this->app->make(Token::class);
            if (!$token->validate()) {
                throw new UserMessageException($token->getErrorMessage());
            }
        }
        $permissions = new Checker();
        if (!$permissions->canAccessGroupSearch()) {
            throw new UserMessageException(t('Access Denied.'));
        }
    }

    /**
     * @return int[]
     */
    protected function getRequestGroupsIDs(): array
    {
        $all = $this->request->request->all();
        $groupIDs = $all['gID'] ?? null;
        if ($groupIDs === null) {
            $all = $this->request->query->all();
            $groupIDs = $all['gID'] ?? [];
        }
        if (!is_array($groupIDs)) {
            $groupIDs = [$groupIDs];
        }
        $groupIDs = array_map(
            static function ($groupID): int {
                return is_numeric($groupID) ? (int) $groupID : 0;
            },
            $groupIDs
        );
        $groupIDs = array_filter(
            $groupIDs,
            static function (int $groupID): bool {
                return $groupID > 0;
            }
        );

        return array_values(array_unique($groupIDs));
    }

    /**
     * @return \Concrete\Core\User\Group\Group[]
     */
    protected function getRequestGroups(?ErrorList $errors = null): array
    {
        $groupIDs = $this->getRequestGroupsIDs();
        if ($groupIDs === []) {
            return [];
        }
        $groups = [];
        $repository = $this->app->make(GroupRepository::class);
        foreach ($groupIDs as $groupID) {
            $group = $repository->getGroupById($groupID);
            if ($group === null) {
                if ($errors !== null) {
                    $errors->add(t('Unable to find the group with ID %s', $groupID));
                }
            } else {
                $groups[] = $group;
            }
        }

        return $groups;
    }
}
