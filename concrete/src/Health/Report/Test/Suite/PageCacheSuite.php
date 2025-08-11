<?php
namespace Concrete\Core\Health\Report\Test\Suite;

use Concrete\Core\Health\Report\Test\Suite;
use Concrete\Core\Health\Report\Test\Test\CheckConfigCacheSettingsForProductionTest;
use Concrete\Core\Health\Report\Test\Test\CheckPagesCustomCacheSettingsTestGroup;

class PageCacheSuite extends Suite
{

    public function __construct()
    {
        $tests = [
            CheckConfigCacheSettingsForProductionTest::class,
            CheckPagesCustomCacheSettingsTestGroup::class,
        ];
        foreach ($tests as $test) {
            $this->add($test);
        }
    }



}
