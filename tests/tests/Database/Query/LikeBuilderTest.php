<?php

namespace Concrete\Tests\Database\Query;

use Concrete\Core\Database\Query\LikeBuilder;
use PHPUnit_Framework_TestCase;

class LikeBuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var LikeBuilder
     */
    private static $defaultInstance;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$defaultInstance = new LikeBuilder();
    }

    public function escapeForLikeProvider()
    {
        return [
            ['', false, false, ''],
            ['', true, true, '%'],
            [1, true, true, '%1%'],
            ['word', false, false, 'word'],
            ['word', false, true, 'word%'],
            ['word', true, false, '%word'],
            ['_', false, false, '\_'],
            ['%', false, false, '\%'],
            ['\\', false, false, '\\\\'],
            ['_%_\\', false, false, '\\_\\%\\_\\\\'],
            ['_%_\\', true, true, '%\\_\\%\\_\\\\%'],
        ];
    }

    /**
     * @dataProvider escapeForLikeProvider
     *
     * @param mixed $input
     * @param mixed $wildcardAtStart
     * @param mixed $wildcardAtEnd
     * @param mixed $expectedOutput
     */
    public function testEscapeForLike($input, $wildcardAtStart, $wildcardAtEnd, $expectedOutput)
    {
        $calculatedOutput = self::$defaultInstance->escapeForLike($input, $wildcardAtStart, $wildcardAtEnd);
        $this->assertSame($expectedOutput, $calculatedOutput);
    }

    public function splitKeywordsForLikeProvider()
    {
        return [
            [null, '\s', true, null],
            ['', '\s', true, null],
            ['   ', '\s', true, null],
            ["\t   \r\n ", '\s', true, null],
            ["\t a  \r b \n ", '\s', false, ['a', 'b']],
            ["\t a  \r b \n ", '\s', true, ['%a%', '%b%']],
            ['This is 100% working', '\s', false, ['This', 'is', '100\\%', 'working']],
            ['This is 100% working', '\s', true, ['%This%', '%is%', '%100\\%%', '%working%']],
        ];
    }

    /**
     * @dataProvider splitKeywordsForLikeProvider
     *
     * @param mixed $input
     * @param mixed $wordSeparators
     * @param mixed $addWildcards
     * @param mixed $expectedOutput
     */
    public function testSplitKeywordsForLike($input, $wordSeparators, $addWildcards, $expectedOutput)
    {
        $calculatedOutput = self::$defaultInstance->splitKeywordsForLike($input, $wordSeparators, $addWildcards);
        $this->assertSame($expectedOutput, $calculatedOutput);
    }
}
