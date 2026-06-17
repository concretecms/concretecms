<?php

namespace Concrete\Core\Board\Instance\Notifier;

defined('C5_EXECUTE') or die("Access Denied.");

interface NotifierInterface
{
    public const BOARD_UPDATE_OPERATION_REFRESH = 10; // Called when an existing object in a board has been updated

    public const BOARD_UPDATE_OPERATION_ADD_NEW = 20; // Called when a new object may be injected into a board.

    /**
     * @return \Concrete\Core\Entity\Board\Instance[]
     */
    public function findBoardInstancesThatMayContainObject($object): array;
}
