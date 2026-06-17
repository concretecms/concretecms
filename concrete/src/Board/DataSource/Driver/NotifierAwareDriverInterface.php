<?php
namespace Concrete\Core\Board\DataSource\Driver;

use Concrete\Core\Board\Instance\Notifier\NotifierInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface NotifierAwareDriverInterface
{
    public function getBoardInstanceNotifier() : NotifierInterface;
}
