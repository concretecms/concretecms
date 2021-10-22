<?php

namespace Concrete\Core\Command\Task\Controller;

use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Command\EmptyResultCommand;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\BatchProcessTaskRunner;
use Concrete\Core\Command\Task\Runner\CommandTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\User\Command\DeleteUserTaskCommand;

defined('C5_EXECUTE') or die("Access Denied.");

class RemoveUnvalidatedUsersController extends AbstractController
{
    /**
     * @var Connection
     */
    protected $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function getName(): string
    {
        return t('Remove Unvalidated Users');
    }

    public function getDescription(): string
    {
        return t('Remove users who never validate their email address long time.');
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        $config = app('config');
        if ($config->get('concrete.user.registration.type') === 'validate_email') {
            $threshold = $config->get('concrete.user.registration.validate_email_threshold');
            if ($threshold) {
                $thresholdDateTime = new \DateTime();
                $thresholdDateTime->sub(new \DateInterval('PT' . $threshold . 'S'));
                $qb = $this->db->createQueryBuilder();
                // Get users recently registered but not validated and also not signed in yet.
                $result = $qb->select('u.uID')
                    ->from('Users', 'u')
                    ->andWhere('u.uIsValidated = :uIsValidated')
                    ->setParameter('uIsValidated', false)
                    ->andWhere('u.uNumLogins = :uNumLogins')
                    ->setParameter('uNumLogins', 0)
                    ->andWhere($qb->expr()->comparison(
                        'u.uDateAdded',
                        '<',
                        $qb->createNamedParameter($thresholdDateTime->format('Y-m-d H:i:s'))
                    ))
                    ->execute();

                if ($result->rowCount() > 0) {
                    $batch = Batch::create();
                    while ($row = $result->fetch()) {
                        $batch->add(new DeleteUserTaskCommand($row['uID']));
                    }
                    return new BatchProcessTaskRunner($task, $batch, $input, t('Removing invalidated users...'));
                }
            }
        }

        return new CommandTaskRunner($task, new EmptyResultCommand(), t('No invalidated users found.'));
    }

}