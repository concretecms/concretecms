<?php

namespace Concrete\Controller\Permissions\Categories\TaskHandlers;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Entity\Board\Board as BoardEntity;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\Category\TaskHandlerInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Duration;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Workflow\Workflow;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class Board extends Controller implements TaskHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\TaskHandlerInterface::handle()
     */
    public function handle(string $task, array $options): ?Response
    {
        $board = $this->getBoard($options);
        if ($board === null) {
            throw new UserMessageException(t('Board not received'));
        }
        $method = lcfirst(camelcase($task));
        if (!method_exists($this, $method)) {
            throw new UserMessageException(t('Unknown permission task: %s', $task));
        }

        return $this->{$method}($board, $options);
    }

    protected function getBoard(array $options): ?BoardEntity
    {
        $boardID = $options['boardID'] ?? null;
        $board = $boardID ? $this->app->make(EntityManagerInterface::class)->find(BoardEntity::class, $boardID) : null;
        if ($board === null) {
            return null;
        }
        $cp = new Checker($board);
        if (!$cp->canEditBoardPermissions()) {
            throw new UserMessageException(t('Access Denied.'));
        }

        return $board;
    }

    protected function addAccessEntity(BoardEntity $board, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($board);
        $pa = Access::getByID($options['paID'], $pk);
        $pe = Entity::getByID($options['peID']);
        $pd = empty($options['pdID']) ? null : Duration::getByID($options['pdID']);
        $pa->addListItem($pe, $pd, $options['accessType']);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function removeAccessEntity(BoardEntity $board, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($board);
        $pa = Access::getByID($options['paID'], $pk);
        $pe = Entity::getByID($options['peID']);
        $pa->removeListItem($pe);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function savePermission(BoardEntity $board, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($board);
        $pa = Access::getByID($options['paID'], $pk);
        $pa->save($options);
        $pa->clearWorkflows();
        $wfIDs = $options['wfID'] ?? null;
        if (is_array($wfIDs)) {
            foreach ($wfIDs as $wfID) {
                $wf = Workflow::getByID($wfID);
                if ($wf !== null) {
                    $pa->attachWorkflow($wf);
                }
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    protected function displayAccessCell(BoardEntity $board, array $options): ?Response
    {
        $pk = Key::getByID($options['pkID']);
        $pk->setPermissionObject($board);
        $this->set('pk', $pk);
        $this->set('pa', Access::getByID($options['paID'], $pk));
        $this->setViewPath('/backend/permissions/labels');

        return null;
    }
}
