<?php
namespace Concrete\Core\Command\Task\Controller;

use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\ProcessTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Page\Sitemap\Command\GenerateSitemapCommand;
use Concrete\Core\User\Command\RemoveOldFileAttachmentsCommand;

defined('C5_EXECUTE') or die("Access Denied.");

class RemoveOldFileAttachmentsController extends AbstractController
{

    public function getName(): string
    {
        return t('Remove Old File Attachments');
    }

    public function getDescription(): string
    {
        return t('Removes all expired file attachments from private messages.');
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        return new ProcessTaskRunner(
            $task,
            new RemoveOldFileAttachmentsCommand(),
            $input,
            t('Removing old file attachments from private messages...')
        );
    }



}
