<?php
namespace Concrete\Core\Health\Report\Test\Suite;

use Concrete\Core\Health\Report\Test\Test\CheckConfigAutomationSettingsForProduction;
use Concrete\Core\Health\Report\Test\Test\CheckConfigErrorSettingsForProductionTest;
use Concrete\Core\Health\Report\Test\Suite;
use Concrete\Core\Health\Report\Test\Test\CheckConfigLoggingSettingsForProduction;
use Concrete\Core\Health\Report\Test\Test\CheckConfigServerSentEventsSettingsForProductionTest;
use Concrete\Core\Health\Report\Test\Test\CheckConfigUrlSettingsForProduction;

class CheckSiteProductionReadinessSuite extends Suite
{

    public function __construct()
    {
        $tests = [
            CheckConfigErrorSettingsForProductionTest::class,
            CheckConfigLoggingSettingsForProduction::class,
            CheckConfigAutomationSettingsForProduction::class,
            CheckConfigServerSentEventsSettingsForProductionTest::class,
            CheckConfigUrlSettingsForProduction::class,
        ];
        foreach ($tests as $test) {
            $this->add($test);
        }
    }



}
