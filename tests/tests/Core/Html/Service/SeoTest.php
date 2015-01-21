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
        $this->assertEquals('Test Site Name :: Second :: First', $seo->getTitle());
        
        $seo->setTitleSegmentSeparator(' | ');
        $this->assertEquals('Test Site Name :: Second | First', $seo->getTitle());
        
        $seo->setTitleFormat('%2$s | %1$s');
        $this->assertEquals('Second | First | Test Site Name', $seo->getTitle());
    }
}
