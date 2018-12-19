<?php

namespace Concrete\Tests\Http;

use Concrete\Core\Http\Request;
use Concrete\Core\Http\RequestMediaTypeParser;
use Mockery;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;

class RequestMediaTypeParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function provideMediaTypeMap()
    {
        return [
            [
                null,
                [],
            ],
            [
                '',
                [],
            ],
            [
                'invalid',
                [],
            ],
            [
                'image/png',
                [
                    'image/png' => ['q' => 1.0],
                ],
            ],
            [
                'image/*; q=0.1, image/png;q=1',
                [
                    'image/png' => ['q' => 1.0],
                    'image/*' => ['q' => 0.1],
                ],
            ],
            [
                'image/*; q=0.1, image/png;q=1;this-is=a-test;really="test it", application/*;   q=0.3; wrong, wont/parse',
                [
                    'image/png' => ['q' => 1.0, 'this-is' => 'a-test', 'really' => '"test it"'],
                    'application/*' => ['q' => 0.3],
                    'image/*' => ['q' => 0.1],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideMediaTypeMap
     *
     * @param string|mixed $acceptValue
     * @param array $expectedMap
     */
    public function testMediaTypeMap($acceptValue, array $expectedMap)
    {
        $mediaTypeParser = $this->getMediaTypeParser($acceptValue);
        $actualMap = $mediaTypeParser->getRequestAcceptMap();

        $this->assertSame($expectedMap, $actualMap);
    }

    /**
     * @return array
     */
    public function provideAcceptMediaType()
    {
        return [
            [null, 'text/html', null, false],
            [['invalid', 'request', 'value'], 'text/html', null, false],
            ['text/html', 'text/html', null, true],
            ['text/*', 'text/html', null, true],
            ['*/*', 'text/html', null, true],
            ['text/html', 'text/html', 0, true],
            ['text/*', 'text/html', 0, true],
            ['*/*', 'text/html', 0, true],
            ['text/html', 'text/html', 0.1, true],
            ['text/*', 'text/html', 0.1, true],
            ['*/*', 'text/html', 0.1, true],
            ['text/html', 'text/html', 0.9, true],
            ['text/*', 'text/html', 0.9, true],
            ['*/*', 'text/html', 0.9, true],
            ['text/html', 'text/html', 1, true],
            ['text/plain,text/html', 'text/html', 1, true],
            ['text/*', 'text/html', 1, true],
            ['*/*', 'text/html', 1, true],
            ['something/else', 'text/html', null, false],
            ['audio/*; q=0.2; v="test"; v2=value1, audio/basic', 'audio/other', null, true],
            ['audio/*; q=0.2; v="test"; v2=value1, audio/basic', 'audio/other', 0.1, true],
            ['audio/*; q=0.2; v="test"; v2=value1, audio/basic', 'audio/other', 0.2, true],
            ['audio/*; q=0.2; v="test"; v2=value1, audio/basic', 'audio/other', 0.3, false],
            ['audio/*; q=0.2; some other malformed value', 'image/*', null, false],
            ['audio/*; q=0.2; some other malformed value', 'audio/mp3', null, true],
        ];
    }

    /**
     * @dataProvider provideAcceptMediaType
     *
     * @param string|mixed $acceptValue
     * @param string|string[] $mediaType
     * @param float|null $minWeight
     * @param bool $expectedResult
     */
    public function testAcceptMediaType($acceptValue, $mediaType, $minWeight, $expectedResult)
    {
        $mediaTypeParser = $this->getMediaTypeParser($acceptValue);
        $actualResult = $mediaTypeParser->isMediaTypeSupported($mediaType, $minWeight);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @param string|mixed $acceptValue
     *
     * @return \Concrete\Core\Http\RequestMediaTypeParser
     */
    protected function getMediaTypeParser($acceptValue)
    {
        $headers = Mockery::mock(HeaderBag::class);
        $headers->shouldReceive('get')->withArgs(['accept'])->andReturn($acceptValue);
        $request = Mockery::mock(Request::class);
        $request->headers = $headers;

        return new RequestMediaTypeParser($request);
    }
}
