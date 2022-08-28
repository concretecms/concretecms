<?php
namespace Concrete\Core\Health\Report\Test\Suite;

use Concrete\Core\Health\Report\Test\Suite;
use Concrete\Core\Health\Report\Test\Test\CheckConfigCacheSettingsForProduction;

class PageCacheSuite extends Suite
{

    public function __construct()
    {
        $tests = [
            CheckConfigCacheSettingsForProduction::class,
        ];
        foreach ($tests as $test) {
            $this->add($test);
        }
    }



}
