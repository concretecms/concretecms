<?php
namespace Concrete\Controller\Backend;

use Concrete\Core\Automation\Process\ProcessFactory;
use Concrete\Core\Automation\Task\Input\Input;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\Automation\Task;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Validation\CSRF\Token;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Automation\Process\Dispatcher\Dispatcher;
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
     * @var Dispatcher
     */
    protected $processDispatcher;

    public function __construct(ErrorList $errorList, Token $token, EntityManager $entityManager, ProcessFactory $processFactory, Dispatcher $processDispatcher)
    {
        parent::__construct();
        $this->errorList = $errorList;
        $this->token = $token;
        $this->entityManager = $entityManager;
        $this->processFactory = $processFactory;
        $this->processDispatcher = $processDispatcher;
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
            $input = new Input();
            $process = $this->processFactory->createProcess($task, $input);
            $response = $this->processDispatcher->dispatch($process);
            return $response;
        }

    }


}