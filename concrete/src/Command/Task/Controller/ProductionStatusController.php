<?php
namespace Concrete\Core\Command\Task\Controller;

use Concrete\Core\Health\Report\Grader\GraderInterface;
use Concrete\Core\Health\Report\Grader\ProductionGrader;
use Concrete\Core\Health\Report\ReportController;
use Concrete\Core\Health\Report\Test\Suite\ProductionStatusSuite;
use Concrete\Core\Health\Report\Test\SuiteInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class ProductionStatusController extends ReportController
{

    public function getName(): string
    {
        return t('Check Site Production Status');
    }

    public function getConsoleCommandName(): string
    {
        return 'health:production-status';
    }

    public function getDescription(): string
    {
        return t('Scans your site and its settings to determine whether it is optimally configured for use in a live, production environment.');
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
