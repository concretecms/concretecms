<?php
namespace Concrete\Core\Health\Report\Test\Test;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Health\Report\Finding\Control\Location\AutomationSettingsLocation;
use Concrete\Core\Health\Report\Finding\Control\Location\LoggingSettingsLocation;
use Concrete\Core\Health\Report\Runner;
use Concrete\Core\Health\Report\Test\TestInterface;

class CheckConfigLoggingSettingsForProductionTest implements TestInterface
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
                t('Log messages are being sent to the database. For increased security and performance, consider logging to a file instead.'),
                $report->button(new LoggingSettingsLocation())
            );
        }
        if ($this->config->get('concrete.processes.logging.method') === 'none') {
            $report->info(
                t('Task process output is not being logged. Consider enabling logging on tasks.'),
                $report->button(new AutomationSettingsLocation())
            );
        }

	}

}
