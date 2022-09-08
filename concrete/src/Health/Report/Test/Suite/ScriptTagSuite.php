<?php
namespace Concrete\Core\Health\Report\Test\Suite;

use Concrete\Core\Health\Report\Test\Suite;
use Concrete\Core\Health\Report\Test\Test\Search\SearchSimpleAttributeContentTest;
use Concrete\Core\Health\Report\Test\Test\Search\SearchSimpleBlockContentTest;

class ScriptTagSuite extends Suite
{

    public function __construct()
    {
        $tests = [
            SearchSimpleAttributeContentTest::class,
            SearchSimpleBlockContentTest::class,
        ];
        foreach ($tests as $test) {
            $this->add($test);
        }
    }



}
