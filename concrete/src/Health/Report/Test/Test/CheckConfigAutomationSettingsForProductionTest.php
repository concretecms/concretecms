<?php
namespace Concrete\Core\Health\Report\Test\Test;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Health\Report\Finding\Control\Location\AutomationSettingsLocation;
use Concrete\Core\Health\Report\Runner;
use Concrete\Core\Health\Report\Test\TestInterface;

class CheckConfigAutomationSettingsForProductionTest implements TestInterface
{

    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function run(Runner $report): void
    {
        if ($this->config->get('concrete.messenger.consume.method') === 'worker') {
            $report->success(
                t('Message queue consumer configured to run via the command line.'),
                $report->button(new AutomationSettingsLocation())
            );
        } else {
            $report->info(
                t('Consider configuring your queue messenger to run via the command line for better efficiency and performance.'),
                $report->button(new AutomationSettingsLocation())
            );
        }
	}

}
