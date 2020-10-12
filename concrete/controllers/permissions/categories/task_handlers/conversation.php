<?php

namespace Concrete\Controller\Permissions\Categories\TaskHandlers;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Conversation\Conversation as ConcreteConversation;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\Category\TaskHandlerInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Duration;
use Concrete\Core\Permission\Key\Key;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class Conversation extends Controller implements TaskHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\TaskHandlerInterface::handle()
     */
    public function handle(string $task, array $options): ?Response
    {
        $conversation = $this->getConversation($options);
        $method = lcfirst(camelcase($task));
        if (!method_exists($this, $method)) {
            throw new UserMessageException(t('Unknown permission task: %s', $task));
        }

        return $this->{$method}($conversation, $options);
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getConversation(array $options): ?ConcreteConversation
    {
        $conversationID = (int) ($options['cnvID'] ?? 0);
        $conversation = $conversationID > 0 ? ConcreteConversation::getByID($conversationID) : null;
        $cpn = new Checker($conversation);
        if (!$cpn->canEditConversationPermissions()) {
            throw new UserMessageException(t('Access Denied.'));
        }

        return $conversation;
    }

    protected function addAccessEntity(?ConcreteConversation $conversation, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($conversation);
        $pa = Access::getByID($options['paID'], $pk);
        $pe = Entity::getByID($options['peID']);
        $pd = empty($options['pdID']) ? null : Duration::getByID($options['pdID']);
        $pa->addListItem($pe, $pd, $options['accessType']);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function removeAccessEntity(?ConcreteConversation $conversation, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($conversation);
        $pa = Access::getByID($options['paID'], $pk);
        $pe = Entity::getByID($options['peID']);
        $pa->removeListItem($pe);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function savePermission(?ConcreteConversation $conversation, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($conversation);
        $pa = Access::getByID($options['paID'], $pk);
        $pa->save($options);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function displayAccessCell(?ConcreteConversation $conversation, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($conversation);
        $this->set('pk', $pk);
        $this->set('pa', Access::getByID($options['paID'], $pk));
        $this->setViewPath('/backend/permissions/labels');

        return null;
    }

    protected function savePermissionAssignments(?ConcreteConversation $conversation, array $options): ?Response
    {
        $permissions = Key::getList('conversations');
        foreach ($permissions as $pk) {
            $pk->setPermissionObject($conversation);
            $pk->clearPermissionAssignment();
            $paID = (int) ($options['pkID'][$pk->getPermissionKeyID()] ?? 0);
            if ($paID > 0) {
                $pa = Access::getByID($paID, $pk);
                if ($pa !== null) {
                    $pk->assignPermissionAccess($pa);
                }
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }
}
