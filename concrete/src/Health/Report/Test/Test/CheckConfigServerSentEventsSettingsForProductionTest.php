<?php
namespace Concrete\Core\Health\Report\Test\Test;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Health\Report\Finding\Details\DashboardPageDetails;
use Concrete\Core\Health\Report\Runner;
use Concrete\Core\Health\Report\Test\TestInterface;

class CheckConfigServerSentEventsSettingsForProductionTest implements TestInterface
{

    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function run(Runner $report): void
    {
        if ($this->config->get('concrete.notification.server_sent_events')) {
            $report->info(
                'Server-sent events are enabled for increased interactivity in the Dashboard. Note: please verify connectivity from the Dashboard page.',
                new DashboardPageDetails('/dashboard/system/notification/events')
            );
        }
	}

}
