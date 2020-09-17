<?php
namespace Concrete\Controller\Backend;

use Concrete\Core\Automation\Process\ProcessFactory;
use Concrete\Core\Automation\Task\TaskDispatcher;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\Automation\Task;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Foundation\Queue\Batch\BatchFactory;
use Concrete\Core\Foundation\Queue\Batch\Response\BatchProcessorResponseFactory;
use Concrete\Core\Foundation\Queue\QueueService;
use Concrete\Core\Foundation\Queue\Response\QueueProgressResponse;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Validation\CSRF\Token;
use Doctrine\ORM\EntityManager;
use League\Tactician\Bernard\QueueCommand;
use Symfony\Component\HttpFoundation\JsonResponse;

class Tasks extends AbstractController
{

    /**
     * @var ErrorList
     */
    protected $errorList;

    /**
     * @var Token
     */
    protected $token;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ProcessFactory
     */
    protected $processFactory;

    /**
     * @var TaskDispatcher
     */
    protected $taskDispatcher;

    public function __construct(ErrorList $errorList, Token $token, EntityManager $entityManager, ProcessFactory $processFactory, TaskDispatcher $taskDispatcher)
    {
        parent::__construct();
        $this->errorList = $errorList;
        $this->token = $token;
        $this->entityManager = $entityManager;
        $this->processFactory = $processFactory;
        $this->taskDispatcher = $taskDispatcher;
    }

    public function execute()
    {
        if (!$this->token->validate('execute', $this->request->request->get('ccm_token'))) {
            $this->errorList->add($this->token->getErrorMessage());
        }

        $page = \Concrete\Core\Page\Page::getByPath('/dashboard/system/automation/tasks');
        $checker = new Checker($page);
        if (!$checker->canViewPage()) {
            $this->errorList->add(t('You do not have permissions to run tasks.'));
        }

        $task = null;
        if ($this->request->request->has('id')) {
            $task = $this->entityManager->find(Task::class, $this->request->request->get('id'));
        }

        if (!$task) {
            $this->errorList->add(t('Invalid task object.'));
        }

        if ($this->errorList->has()) {
            return new JsonResponse($this->errorList);
        } else {
            $process = $this->processFactory->createProcess($task);
            $this->taskDispatcher->dispatch($process);
            return new JsonResponse($process);
        }

    }


}