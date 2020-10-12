<?php

namespace Concrete\Controller\Permissions\Categories\TaskHandlers;

use Concrete\Core\Entity\Board\Board as BoardEntity;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Permission\Category\ObjectTaskHandler;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\ObjectInterface;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class Board extends ObjectTaskHandler
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\ObjectTaskHandler::$hasWorkflows
     */
    protected $hasWorkflows = true;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Category\ObjectTaskHandler::getPermissionObject()
     */
    protected function getPermissionObject(array $options): ObjectInterface
    {
        $boardID = $options['boardID'] ?? null;
        $board = $boardID ? $this->app->make(EntityManagerInterface::class)->find(BoardEntity::class, $boardID) : null;
        if ($board === null) {
            throw new UserMessageException(t('Board not received'));
        }
        $cp = new Checker($board);
        if (!$cp->canEditBoardPermissions()) {
            throw new UserMessageException(t('Access Denied.'));
        }

        return $board;
    }
}
