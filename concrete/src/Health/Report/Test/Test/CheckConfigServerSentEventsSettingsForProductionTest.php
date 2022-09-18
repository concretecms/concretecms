<?php
namespace Concrete\Core\Health\Report\Test\Test;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Health\Report\Finding\Control\Location\ServerSentEventsSettingsLocation;
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
            $report->success(
                t('Server-sent events are enabled for increased interactivity in the Dashboard. Note: please verify connectivity from the Dashboard page.'),
                $report->button(new ServerSentEventsSettingsLocation())
            );
        }
	}

}
