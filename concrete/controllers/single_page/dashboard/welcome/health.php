<?php
namespace Concrete\Controller\SinglePage\Dashboard\Welcome;

use Concrete\Core\Command\Task\Input\Input;
use Concrete\Core\Command\Task\Runner\Context\ContextFactory;
use Concrete\Core\Command\Task\TaskService;
use Concrete\Core\Health\Report\ReportControllerInterface;
use Concrete\Core\Health\Report\Result\ResultList;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Search\Pagination\PaginationFactory;

class Health extends DashboardPageController
{

    public function view()
    {
        $this->setThemeViewTemplate('desktop/empty.php');
        $this->loadlatestResults();
        $this->loadReports();
    }

    protected function loadReports()
    {
        $reports = [];
        $tasks = $this->app->make(TaskService::class)->getList();
        foreach ($tasks as $task) {
            if ($task->getController() instanceof ReportControllerInterface) {
                $reports[] = $task;
            }
        }
        $this->set('reports', $reports);
    }

    protected function loadLatestResults()
    {
        $list = new ResultList($this->entityManager);
        $list->setItemsPerPage(5);
        $pagination = $this->app->make(PaginationFactory::class)->createPaginationObject($list);
        $this->set('results', $pagination);
    }

    public function run_report()
    {
        if (!$this->token->validate('run_report')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $task = $this->app->make(TaskService::class)->getByID($this->request->request->get('task'));
        if (!$task) {
            $this->error->add(t('Invalid task specified.'));
        } else {
            $controller = $task->getController();
            if (!($controller instanceof ReportControllerInterface)) {
                $this->error->add(t('Task %s does not implement the HealthReportControllerInterface'), $controller->getName());
            }
        }
        if (!$this->error->has()) {

            $runner = $controller->getTaskRunner($task, new Input());
            $handler = $this->app->make($runner->getTaskRunnerHandler());
            $handler->boot($runner);

            $contextFactory = $this->app->make(ContextFactory::class);
            $context = $contextFactory->createDashboardContext($runner);

            $handler->start($runner, $context);
            $handler->run($runner, $context);
            
            $this->flash('success', t('Report started successfully.'));
            return $this->buildRedirect($this->action('view'));
        }
    }
}
