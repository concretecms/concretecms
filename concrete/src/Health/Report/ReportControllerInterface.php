<?php
namespace Concrete\Core\Health\Report;

use Concrete\Core\Command\Task\Controller\ControllerInterface;
use Concrete\Core\Health\Report\Test\SuiteInterface;

interface ReportControllerInterface extends ControllerInterface
{

    /**
     * @return SuiteInterface
     */
    public function getTestSuite(): SuiteInterface;

}
