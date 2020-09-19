<?php
namespace Concrete\Core\Automation\Task\Controller;

use Concrete\Core\Automation\Process\Response\CompletedWithSuccessResponse;
use Concrete\Core\Automation\Process\Response\ResponseInterface;
use Concrete\Core\Automation\Task\Input\InputInterface;
use Concrete\Core\Cache\Command\ClearCacheCommand;
use Concrete\Core\Foundation\Command\CommandInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class ClearCacheController extends AbstractController
{

    public function getName(): string
    {
        return t('Clear Cache');
    }

    public function getDescription(): string
    {
        return t('Clears all caches.');
    }

    public function getCommand(InputInterface $input): CommandInterface
    {
        return new ClearCacheCommand();
    }

}
