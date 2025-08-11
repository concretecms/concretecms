<?php
namespace Concrete\Controller\SinglePage\Dashboard\Welcome;

use Concrete\Core\Command\Task\TaskService;
use Concrete\Core\Command\Task\Traits\DashboardTaskRunnerTrait;
use Concrete\Core\Entity\Command\TaskProcess;
use Concrete\Core\Health\Grade\PassFailGrade;
use Concrete\Core\Health\Report\ReportControllerInterface;
use Concrete\Core\Health\Report\Result\ResultList;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Production\Modes;
use Concrete\Core\Search\Pagination\PaginationFactory;

class Health extends DashboardPageController
{

    use DashboardTaskRunnerTrait;

    const SITE_MODE_DEVELOPMENT = 5;
    const SITE_MODE_STAGING = 15;
    const SITE_MODE_PRODUCTION_NO_TEST = 20;
    const SITE_MODE_PRODUCTION_PASSING = 50;
    const SITE_MODE_PRODUCTION_FAILING = 99;

    public function view()
    {
        $this->set('dateService', $this->app->make(Date::class));
        $this->setThemeViewTemplate('desktop/empty.php');
        $this->loadlatestResults();
        $this->loadReports();
        $this->loadRunningReportProcesses();
        $this->loadProductionStatus();
    }

    protected function loadProductionStatus()
    {
        $config = $this->app->make("config");
        $productionStatus = $config->get('concrete.security.production.mode');
        if ($productionStatus === Modes::MODE_DEVELOPMENT) {
            $productionStatus = self::SITE_MODE_DEVELOPMENT;
            $productionStatusClass = 'text-bg-info';
        }
        if ($productionStatus === Modes::MODE_STAGING) {
            $productionStatus = self::SITE_MODE_STAGING;
            $productionStatusClass = 'text-bg-info';
        }
        if ($productionStatus === Modes::MODE_PRODUCTION) {
            // Let's see if we have a recent production test result.
            $latestTestResult = null;
            $task = $this->app->make(TaskService::class)->getByHandle('production_status');
            if ($task) {
                $latestTestResult = ResultList::getLatestResult($task);
                if ($latestTestResult) {
                    /**
                     * @var $grade PassFailGrade
                     */
                    $grade = $latestTestResult->getGrade();
                    if ($grade->hasPassed()) {
                        $productionStatus = self::SITE_MODE_PRODUCTION_PASSING;
                        $productionStatusClass = 'text-bg-success';
                    } else {
                        $productionStatus = self::SITE_MODE_PRODUCTION_FAILING;
                        $productionStatusClass = 'text-bg-danger';
                    }
                }
            }
            if (!$latestTestResult) {
                $productionStatus = self::SITE_MODE_PRODUCTION_NO_TEST;
                $productionStatusClass = 'text-bg-warning';
            }
        }
        $this->set('productionStatus', $productionStatus);
        $this->set('productionStatusClass', $productionStatusClass);
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

    protected function loadRunningReportProcesses()
    {
        $r = $this->entityManager->getRepository(TaskProcess::class);
        $runningReportProcesses = [];
        $runningProcesses = $r->findBy(['dateCompleted' => null], ['dateCompleted' => 'desc']);
        foreach ($runningProcesses as $runningProcess) {
            $controller = $runningProcess->getTask()->getController();
            if ($controller instanceof ReportControllerInterface) {
                $runningReportProcesses[] = $runningProcess;
            }
        }
        $this->set('runningReportProcesses', $runningReportProcesses);
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

            $this->executeTask($task);

            $this->flash('success', t('Report started successfully.'));
            return $this->buildRedirect($this->action('view'));
        }
    }
}
