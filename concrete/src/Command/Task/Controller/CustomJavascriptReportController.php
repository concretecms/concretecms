<?php
namespace Concrete\Core\Command\Task\Controller;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Entity\Health\Report\Result;
use Concrete\Core\Entity\Health\Report\SearchResult;
use Concrete\Core\Health\Report\Grader\GraderInterface;
use Concrete\Core\Health\Report\Grader\ProductionGrader;
use Concrete\Core\Health\Report\ReportController;
use Concrete\Core\Health\Report\Test\Suite\ProductionStatusSuite;
use Concrete\Core\Health\Report\Test\Suite\ScriptTagSuite;
use Concrete\Core\Health\Report\Test\SuiteInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class CustomJavascriptReportController extends ReportController implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    public function getName(): string
    {
        return t('Find Custom JavaScript');
    }

    public function getConsoleCommandName(): string
    {
        return 'health:javascript';
    }

    public function getDescription(): string
    {
        return t('Scans block and attribute content for custom JavaScript added to pages.');
    }

    public function getTestSuite(): SuiteInterface
    {
        return $this->app->make(ScriptTagSuite::class);
    }

    protected function getResult(TaskInterface $task, InputInterface $input): Result
    {
        return new SearchResult(SearchResult::TYPE_TAG, 'script');
    }

    public function getResultGrader(): ?GraderInterface
    {
        return null;
    }
}
