<?php
namespace Concrete\Tests\Core\Html\Service;

class SeoTest extends \PHPUnit_Framework_TestCase
{
    public function testSeoTitle()
    {
        $seo = new \Concrete\Core\Html\Service\Seo();
        $seo->setSiteName('Test Site Name');
        $seo->addTitleSegment('First');
        $this->assertEquals('Test Site Name :: First', $seo->getTitle());

        $seo->addTitleSegment('Second');
        $this->assertEquals('Test Site Name :: First :: Second', $seo->getTitle());

        $seo->setTitleSegmentSeparator(' | ');
        $this->assertEquals('Test Site Name :: First | Second', $seo->getTitle());

        $seo->setTitleFormat('%2$s | %1$s');
        $this->assertEquals('First | Second | Test Site Name', $seo->getTitle());

        $seo->addTitleSegmentBefore('Third');
        $this->assertEquals('Third | First | Second | Test Site Name', $seo->getTitle());

        $seo->setCustomTitle('This is the custom title');
        $this->assertEquals('This is the custom title | Test Site Name', $seo->getTitle());
    }
}
