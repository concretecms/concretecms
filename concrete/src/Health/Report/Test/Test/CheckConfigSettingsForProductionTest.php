<?php
namespace Concrete\Core\Health\Report\Test\Test;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Health\Report\Runner;
use Concrete\Core\Health\Report\Test\TestInterface;

class CheckConfigSettingsForProductionTest implements TestInterface
{

    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function run(Runner $report): void
    {
        if ($this->config->get('concrete.debug.display_errors') && $this->config->get('concrete.debug.detail') == 'debug') {
            $report->alert('Debug Error Output is currently set to detail. Should be disabled.');
		}
	}

}
