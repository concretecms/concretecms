<?php
namespace Concrete\Tests\Core\Search;

use PHPUnit_Framework_TestCase;

class ItemListTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Fixtures\UserListFixture
     */
    private static $userListFixture;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$userListFixture = new Fixtures\UserListFixture();
    }

    public function splitKeywordsProvider()
    {
        return [
            ['', null],
            [$this, null],
            [false, null],
            ['   word  ', ['%word%']],
            ['This is a sentence', ['%This%', '%is%', '%a%', '%sentence%']],
            ['%Special_chars', ['%\%Special\_chars%']],
            ['Hello, world!', ['%Hello,%', '%world!%']],
            ['Hello, world!', ['%Hello%', '%world%'], '\s,!'],
        ];
    }

    /**
     * @dataProvider splitKeywordsProvider
     */
    public function testSplitKeywords($text, $expectedKeywords, $wordSeparators = null)
    {
        if ($wordSeparators === null) {
            $calculatedKeywords = self::$userListFixture->splitKeywordsWrapper($text);
        } else {
            $calculatedKeywords = self::$userListFixture->splitKeywordsWrapper($text, $wordSeparators);
        }
        $this->assertSame($expectedKeywords, $calculatedKeywords);
    }
}
