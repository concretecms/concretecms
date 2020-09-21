<?php

namespace Concrete\Controller\Backend;

use Concrete\Core\Automation\Task\Input\Input;
use Concrete\Core\Automation\Task\Response\HttpResponseFactory;
use Concrete\Core\Automation\Task\Runner\TaskRunner;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\Automation\Task;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Validation\CSRF\Token;
use Doctrine\ORM\EntityManager;
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
     * @var TaskRunner
     */
    protected $taskRunner;

    /**
     * @var HttpResponseFactory
     */
    protected $httpResponseFactory;

    public function __construct(
        ErrorList $errorList,
        Token $token,
        EntityManager $entityManager,
        TaskRunner $taskRunner,
        HttpResponseFactory $httpResponseFactory
    ) {
        parent::__construct();
        $this->errorList = $errorList;
        $this->token = $token;
        $this->entityManager = $entityManager;
        $this->taskRunner = $taskRunner;
        $this->httpResponseFactory = $httpResponseFactory;
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
            /**
             * @var $task Task
             */
            $task = $this->entityManager->find(Task::class, $this->request->request->get('id'));
        }

        if (!$task) {
            $this->errorList->add(t('Invalid task object.'));
        }

        if ($this->errorList->has()) {
            return new JsonResponse($this->errorList);
        } else {
            $input = new Input();
            $command = $task->getController()->getTaskRunnerCommand($task, $input);
            $response = $this->taskRunner->run($command);
            return $this->httpResponseFactory->createResponse($response);
        }
    }


}