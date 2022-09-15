<?php
namespace Concrete\Core\Health\Report\Test\Test;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Health\Report\Finding\Control\Location\DebugSettingsLocation;
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
        if ($this->config->get('concrete.debug.display_errors')) {
            if ($this->config->get('concrete.debug.detail') == 'debug') {
                $report->alert(
                    t('Debug Error Output is currently set to detail. Debug errors should be disabled and errors should not be displayed in-page.'),
                    $report->button(new DebugSettingsLocation()),
                );
            } else {
                $report->warning(
                    t('Error output is being displayed in page. Please disable in-page error reporting.'),
                    $report->button(new DebugSettingsLocation()),
                );
            }
		}


	}

}
