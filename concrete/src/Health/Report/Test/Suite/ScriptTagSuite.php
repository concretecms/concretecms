<?php
namespace Concrete\Core\Health\Report\Test\Suite;

use Concrete\Core\Health\Report\Test\Suite;
use Concrete\Core\Health\Report\Test\Test\Search\SearchSimpleAttributeContentTest;

class ScriptTagSuite extends Suite
{

    public function __construct()
    {
        $tests = [
            SearchSimpleAttributeContentTest::class,
        ];
        foreach ($tests as $test) {
            $this->add($test);
        }
    }



}
