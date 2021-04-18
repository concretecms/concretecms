<?php

namespace Concrete\Controller\Backend;

use Concrete\Core\Command\Scheduler\Scheduler;
use Concrete\Core\Command\Task\Input\InputFactory;
use Concrete\Core\Command\Task\Output\OutputFactory;
use Concrete\Core\Command\Task\Response\HttpResponseFactory;
use Concrete\Core\Command\Scheduler\Response\HttpResponseFactory as ScheduleHttpResponseFactory;
use Concrete\Core\Command\Task\Runner\Context\ContextFactory;
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
     * @var HttpResponseFactory
     */
    protected $httpResponseFactory;

    /**
     * @var ScheduleHttpResponseFactory
     */
    protected $scheduleHttpResponseFactory;

    public function __construct(
        ErrorList $errorList,
        Token $token,
        EntityManager $entityManager,
        HttpResponseFactory $httpResponseFactory,
        ScheduleHttpResponseFactory $scheduleHttpResponseFactory
    ) {
        parent::__construct();
        $this->errorList = $errorList;
        $this->token = $token;
        $this->entityManager = $entityManager;
        $this->httpResponseFactory = $httpResponseFactory;
        $this->scheduleHttpResponseFactory = $scheduleHttpResponseFactory;
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
            /**
             * @var $inputFactory InputFactory
             * @var $contextFactory ContextFactory
             */
            $inputFactory = $this->app->make(InputFactory::class);
            $contextFactory = $this->app->make(ContextFactory::class);
            $input = $inputFactory->createFromRequest($this->request, $task->getController()->getInputDefinition());

            if ($this->request->request->get('scheduleTask')) {
                $scheduler = $this->app->make(Scheduler::class);
                $scheduledTask = $scheduler->createScheduledTask($task, $input, $this->request->request->get('cronExpression'));
                return $this->scheduleHttpResponseFactory->createResponse($scheduledTask);
            } else {
                $runner = $task->getController()->getTaskRunner($task, $input);
                $handler = $this->app->make($runner->getTaskRunnerHandler());
                $handler->boot($runner);

                $context = $contextFactory->createDashboardContext($runner);

                $handler->start($runner, $context);
                $handler->run($runner, $context);
                $response = $handler->complete($runner, $context);

                return $this->httpResponseFactory->createResponse($response);
            }
        }
    }


}