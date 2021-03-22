<?php

namespace Concrete\Core\Command\Task\Controller;

use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Command\Task\Input\Definition\BooleanField;
use Concrete\Core\Command\Task\Input\Definition\Definition;
use Concrete\Core\Command\Task\Input\Definition\Field;
use Concrete\Core\Command\Task\Input\Definition\SelectField;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Search\Index\IndexManagerInterface;
use Concrete\Core\Search\Index\IndexObjectProvider;

defined('C5_EXECUTE') or die("Access Denied.");

class ReindexContentController extends AbstractController
{

    use ApplicationAwareTrait;

    /**
     * @var IndexManagerInterface
     */
    protected $indexManager;

    /**
     * @param IndexManagerInterface $indexManager
     */
    public function __construct(IndexManagerInterface $indexManager)
    {
        $this->indexManager = $indexManager;
    }

    public function getName(): string
    {
        return t('Reindex Content');
    }

    public function getDescription(): string
    {
        return t('Reindex pages, files, users and Express objects.');
    }

    public function getInputDefinition(): ?Definition
    {
        $definition = new Definition();
        $definition->addField(new BooleanField('clear', t('Clear Index'), t('Clear index before reindexing.')));
        $definition->addField(new Field('after', t('After ID'), t('Reindex objects after a particular ID.')));
        $definition->addField(
            new SelectField(
                'object',
                t('Object to reindex.'),
                t('You must provide what type of object you want to reindex.'),
                [
                    'pages' => t('Pages'),
                    'files' => t('Files'),
                    'users' => t('Users'),
                    'express' => t('Express'),
                ],
                true
            )
        );
        return $definition;
    }


    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        $object = (string) $input->getField('object')->getValue();

        if ($input->hasField('clear')) {

            if ($object == 'pages') {

            }
        }
    }


}
