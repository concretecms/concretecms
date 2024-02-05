<?php

namespace Concrete\Tests\View;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\View;
use Concrete\Core\Entity\Attribute\Type as AttributeType;
use Mockery;
use PHPUnit\Framework\TestCase;

class AttributeViewTest extends TestCase
{

    public static function actionProvider()
    {
        return [
            [
                ['foo'],
                'http://www.dummyco.com/path/to/server/index.php/ccm/system/attribute/action/key/123/foo',
            ],
            [
                ['foo', 'bar'],
                'http://www.dummyco.com/path/to/server/index.php/ccm/system/attribute/action/key/123/foo/bar',
            ],
        ];
    }

    /**
     * @param array $arguments
     * @param string $expectedResult
     * @dataProvider actionProvider
     */
    public function testAction(array $arguments, $expectedResult)
    {
        $attributeType = Mockery::mock(AttributeType::class);
        $attributeType
            ->shouldReceive('getPackageHandle')
            ->withNoArgs()
            ->andReturn('')
        ;
        $attributeKey = Mockery::mock(AttributeKeyInterface::class);
        $attributeKey
            ->shouldReceive('getAttributeType')
            ->withNoArgs()
            ->andReturn($attributeType)
        ;
        $attributeKey
            ->shouldReceive('getAttributeKeyID')
            ->withNoArgs()
            ->andReturn(123)
        ;
        $view = new View($attributeKey);
        $actualResult = call_user_func_array([$view, 'action'], $arguments);
        $this->assertSame($expectedResult, $actualResult);
    }
}
