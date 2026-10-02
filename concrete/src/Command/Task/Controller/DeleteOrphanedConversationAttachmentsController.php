<?php

declare(strict_types=1);

namespace Concrete\Core\Command\Task\Controller;

use Concrete\Core\Command\Task\Input\Definition\Definition;
use Concrete\Core\Command\Task\Input\Definition\IntegerField;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\ProcessTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Conversation\Command\DeleteOrphanedConversationAttachmentsCommand;
use Punic\Unit;

defined('C5_EXECUTE') or die('Access Denied.');

class DeleteOrphanedConversationAttachmentsController extends AbstractController
{
    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function getName(): string
    {
        return t('Delete Orphaned Conversation Attachments');
    }

    public function getDescription(): string
    {
        return t('Deletes the files uploaded to conversations that have never been attached to a message.');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Command\Task\Controller\AbstractController::getInputDefinition()
     */
    public function getInputDefinition(): ?Definition
    {
        $definition = new Definition();
        $defaultMinAge = max(0, (int) $this->config->get('conversations.attachments_orphaned_min_age', 28800));
        $definition->addField(new IntegerField(
            'min-age',
            t('Minimum age (seconds)'),
            t(
                'Number of seconds after which the uploaded files not attached to a message are deleted (leave empty to use the default value: %s).',
                Unit::format($defaultMinAge, 'duration/second', 'long')
            ),
            0
        ));
        $definition->addField(new IntegerField(
            'limit',
            t('Limit'),
            t('Maximum number of files to be deleted (leave empty to delete all the orphaned files).'),
            1
        ));

        return $definition;
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        $command = new DeleteOrphanedConversationAttachmentsCommand();
        if ($input->hasField('min-age')) {
            $command->setMinAge((int) $input->getField('min-age')->getValue());
        }
        if ($input->hasField('limit')) {
            $command->setLimit((int) $input->getField('limit')->getValue());
        }

        return new ProcessTaskRunner(
            $task,
            $command,
            $input,
            t('Deleting orphaned conversation attachments...')
        );
    }
}
