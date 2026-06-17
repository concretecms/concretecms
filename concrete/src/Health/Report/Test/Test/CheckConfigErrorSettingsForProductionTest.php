<?php

namespace Concrete\Core\Health\Report\Test\Test;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Health\Report\Finding\Control\Location\ErrorHandlingSettingsLocation;
use Concrete\Core\Health\Report\Runner;
use Concrete\Core\Health\Report\Test\TestInterface;

class CheckConfigErrorSettingsForProductionTest implements TestInterface
{

    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function run(Runner $report): void
    {
        if ($this->config->get('concrete.error.display.guests') === 'debug') {
            $report->alert(
                t(
                    'Errors are configured to display their full debug output. Debug errors should be disabled.'
                ),
                $report->button(new ErrorHandlingSettingsLocation()),
            );
        } elseif ($this->config->get('concrete.error.display.guests') === 'message') {
            $report->warning(
                t(
                    'Error messages are being displayed in page. It is advisable to change this setting to a generic error message.'
                ),
                $report->button(new ErrorHandlingSettingsLocation()),
            );
        }
    }

}
