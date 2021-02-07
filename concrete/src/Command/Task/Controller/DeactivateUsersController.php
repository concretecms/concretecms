<?php
namespace Concrete\Core\Command\Task\Controller;

use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\ProcessTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Page\Sitemap\Command\GenerateSitemapCommand;
use Concrete\Core\User\Command\DeactivateUsersCommand;

defined('C5_EXECUTE') or die("Access Denied.");

class DeactivateUsersController extends AbstractController
{

    public function getName(): string
    {
        return t('Deactivate Users');
    }

    public function getDescription(): string
    {
        return t('Deactivates users who haven\'t logged in recently, if automatic user deactivation is active.');
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        return new ProcessTaskRunner(
            $task,
            new DeactivateUsersCommand(),
            $input,
            t('Starting deactivate users command...')
        );
    }



}
