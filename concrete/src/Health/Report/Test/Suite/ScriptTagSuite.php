<?php
namespace Concrete\Core\Health\Report\Test\Suite;

use Concrete\Core\Health\Report\Test\Suite;
use Concrete\Core\Health\Report\Test\Test\Search\FaqBlockTest;
use Concrete\Core\Health\Report\Test\Test\Search\GalleryBlockTest;
use Concrete\Core\Health\Report\Test\Test\Search\ImageSliderBlockTest;
use Concrete\Core\Health\Report\Test\Test\Search\SimpleAttributeContentTest;
use Concrete\Core\Health\Report\Test\Test\Search\SimpleBlockContentTest;
use Concrete\Core\Health\Report\Test\Test\Search\SurveyBlockTest;

class ScriptTagSuite extends Suite
{

    public function __construct()
    {
        $tests = [
            SimpleAttributeContentTest::class,
            SimpleBlockContentTest::class,
            FaqBlockTest::class,
            SurveyBlockTest::class,
            GalleryBlockTest::class,
            ImageSliderBlockTest::class,
        ];
        foreach ($tests as $test) {
            $this->add($test);
        }
    }



}
