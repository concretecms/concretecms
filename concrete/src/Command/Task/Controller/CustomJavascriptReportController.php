<?php
namespace Concrete\Core\Command\Task\Controller;

use Concrete\Core\Health\Report\Grader\GraderInterface;
use Concrete\Core\Health\Report\Grader\ProductionGrader;
use Concrete\Core\Health\Report\ReportController;
use Concrete\Core\Health\Report\Test\Suite\ProductionStatusSuite;
use Concrete\Core\Health\Report\Test\SuiteInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class CustomJavascriptReportController extends ReportController
{

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
        return new ProductionStatusSuite();
    }

    public function getResultGrader(): ?GraderInterface
    {
        return new ProductionGrader();
    }
}
