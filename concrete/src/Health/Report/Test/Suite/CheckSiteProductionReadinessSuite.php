<?php
namespace Concrete\Core\Health\Report\Test\Suite;

use Concrete\Core\Health\Report\Test\Test\CheckConfigSettingsForProductionTest;
use Concrete\Core\Health\Report\Test\Suite;

class CheckSiteProductionReadinessSuite extends Suite
{

    public function __construct()
    {
        $this->add(CheckConfigSettingsForProductionTest::class);
    }



}
