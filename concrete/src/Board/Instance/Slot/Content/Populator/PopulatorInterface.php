<?php

namespace Concrete\Core\Board\Instance\Slot\Content\Populator;

use Concrete\Core\Board\Instance\Logger\LoggerInterface;
use Concrete\Core\Board\Instance\Slot\Content\ObjectInterface;
use Concrete\Core\Board\Instance\Item\Data\DataInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface PopulatorInterface
{

    /**
     * @return string
     */
    public function getDataClass() : string;

    /**
     * @param DataInterface $data
     * @param LoggerInterface $logger
     * @param bool $enforceViewPermissions Whether the resulting content must be restricted to what the
     *                                     current user is permitted to view. Board generation runs from
     *                                     the console and from queue workers, where there is no session
     *                                     and therefore no meaningful current user, so this defaults to
     *                                     false. Callers that serve content straight back to a browser in
     *                                     response to user-supplied input must pass true.
     * @return array
     */
    public function createContentObjects(DataInterface $data, LoggerInterface $logger, bool $enforceViewPermissions = false) : array;

}
