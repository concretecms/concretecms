<?php
namespace Concrete\Tests\Core\Url\Components;

use Concrete\Core\Url\Components\Query;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $query = new Query('?foo=bar');
        $this->assertEquals('foo=bar', (string) $query);
        
        $query = new Query('?query=移設');
        $this->assertEquals('query=%E7%A7%BB%E8%A8%AD', (string) $query);
    }
    
    public function testToStringFromArray()
    {
        $query = new Query('?foo[]=a&foo[]=b&foo[]=c&bar=d');
        $this->assertEquals('foo%5B%5D=a&foo%5B%5D=b&foo%5B%5D=c&bar=d', (string) $query);
    }
}
