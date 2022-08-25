<?php
namespace Concrete\Core\Health\Report\Test\Test;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Health\Report\Finding\Details\DashboardPageDetails;
use Concrete\Core\Health\Report\Runner;
use Concrete\Core\Health\Report\Test\TestInterface;

class CheckConfigLoggingSettingsForProduction implements TestInterface
{

    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function run(Runner $report): void
    {
        if ($this->config->get('concrete.log.configuration.mode') === 'simple' && $this->config->get('concrete.log.configuration.simple.handler') === 'database') {
            $report->info(
                'Log messages are being sent to the database. For increased security and performance, consider logging to a file instead.',
                new DashboardPageDetails('/dashboard/system/environment/logging')
            );
        }
        if ($this->config->get('concrete.processes.logging.method') === 'none') {
            $report->info(
                'Task process output is not being logged. Consider enabling logging on tasks.',
                new DashboardPageDetails('/dashboard/system/automation/settings')
            );
        }

	}

}
