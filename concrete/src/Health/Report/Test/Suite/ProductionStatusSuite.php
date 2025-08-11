<?php
namespace Concrete\Core\Health\Report\Test\Suite;

use Concrete\Core\Health\Report\Test\Test\CheckConfigAutomationSettingsForProductionTest;
use Concrete\Core\Health\Report\Test\Test\CheckConfigCacheSettingsForProductionTest;
use Concrete\Core\Health\Report\Test\Test\CheckConfigErrorSettingsForProductionTest;
use Concrete\Core\Health\Report\Test\Suite;
use Concrete\Core\Health\Report\Test\Test\CheckConfigLoggingSettingsForProductionTest;
use Concrete\Core\Health\Report\Test\Test\CheckConfigServerSentEventsSettingsForProductionTest;
use Concrete\Core\Health\Report\Test\Test\CheckConfigUrlSettingsForProductionTest;

class ProductionStatusSuite extends Suite
{

    public function __construct()
    {
        $tests = [
            CheckConfigErrorSettingsForProductionTest::class,
            CheckConfigLoggingSettingsForProductionTest::class,
            CheckConfigAutomationSettingsForProductionTest::class,
            CheckConfigServerSentEventsSettingsForProductionTest::class,
            CheckConfigCacheSettingsForProductionTest::class,
            CheckConfigUrlSettingsForProductionTest::class,
        ];
        foreach ($tests as $test) {
            $this->add($test);
        }
    }



}
