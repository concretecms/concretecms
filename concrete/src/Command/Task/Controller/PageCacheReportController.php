<?php
namespace Concrete\Core\Command\Task\Controller;

use Concrete\Core\Health\Report\Grader\GraderInterface;
use Concrete\Core\Health\Report\Grader\PageCacheReportGrader;
use Concrete\Core\Health\Report\ReportController;
use Concrete\Core\Health\Report\Test\Suite\PageCacheSuite;
use Concrete\Core\Health\Report\Test\SuiteInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class PageCacheReportController extends ReportController
{

    public function getName(): string
    {
        return t('Check Page Cache Settings');
    }

    public function getConsoleCommandName(): string
    {
        return 'health:page-cache';
    }

    public function getDescription(): string
    {
        return t('Checks page cache settings globally and on every page to determine whether they are optimally configured for use in a live, production environment.');
    }

    public function getTestSuite(): SuiteInterface
    {
        return new PageCacheSuite();
    }

    public function getResultGrader(): ?GraderInterface
    {
        return new PageCacheReportGrader();
    }
}
