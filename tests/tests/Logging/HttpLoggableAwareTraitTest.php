<?php

namespace Concrete\Tests\Logging;

use Concrete\Core\Logging\HttpLoggableAwareTrait;
use PHPUnit_Framework_TestCase;

class HttpLoggableAwareTraitTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider routeDestinationProvider
     *
     * @return array
     */
    public function contentTypesProvider()
    {
        return [
            // contentType, expected
            // true = loggable http body types
            [['content-type' => 'text/plain'], true],
            [['content-type' => 'text/css'], true],
            [['content-type' => 'text/html'], true],
            [['content-type' => 'text/csv'], true],
            [['content-type' => 'application/json'], true],
            [['content-type' => 'application/xml'], true],
            [['content-type' => 'application/rss+xml'], true],
            // false
            [['content-type' => 'application/rtf'], false],
            [['content-type' => 'application/javascript'], false],
            [['content-type' => 'image/png'], false],
            [['content-type' => 'image/jpeg'], false]
        ];
    }

    /**
     * Stub config values from log -> http -> content-types
     *
     * @return array
     */
    public function getLoggableContentTypes()
    {
        return [
            '#^text/#i',
            '#^application/json$#i',
            '#^application/(.*\+)?xml$#i',
        ];
    }

    /**
     * @dataProvider contentTypesProvider
     */
    public function testIsLoggable($contentType, $expected)
    {
        $mockTrait = $this->getMockForTrait(HttpLoggableAwareTrait::class);
        $mockTrait->expects($this->any())
            ->method('getLoggableContentTypes')
            ->willReturn($this->getLoggableContentTypes());

        $this->assertEquals($expected, $mockTrait->isLoggable($contentType, $mockTrait->getLoggableContentTypes()));
    }
}
