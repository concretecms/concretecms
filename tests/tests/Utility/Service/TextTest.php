<?php

namespace Concrete\Tests\Utility\Service;

use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Core;

class TextTest extends ConcreteDatabaseTestCase
{
    /**
     * @var TextHelper
     */
    protected $object;

    protected $fixtures = [];
    protected $tables = ['ConfigStore'];

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Concrete\Core\Utility\Service\Text();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->object);
        parent::tearDown();
    }

    public function asciifyDataProvider()
    {
        return [
            ['Mixed with English and Germaen', 'Mixed with English and Germän', 'de_DE'],
            ['Mixed with English and ', 'Mixed with English and 日本人', ''],
            ['Mixed with English and .doc', 'Mixed with English and 日本人.doc', ''],
            ['Mixed with English and .', 'Mixed with English and 日本人.日本人', ''],
            ['', '日本人', ''],
            ['.doc', '日本人.doc', ''],
            ['.', '日本人.日本人', ''],
        ];
    }

    public function urlifyDataProvider()
    {
        return [
            ['jetudie-le-francais', " J'étudie le français "],
            ['lo-siento-no-hablo-espanol', 'Lo siento, no hablo español.'],
            ['f3pws', 'ΦΞΠΏΣ'],
            ['yo-hablo-espanol', '¿Yo hablo español?'],
        ];
    }

    public function shortenDataProvider()
    {
        return [
            ['This is a simple test...', 'This is a simple test case', 24, '...'],
            ['This is a simple test etc', 'This is a simple test case', 22, ' etc'],
            ['This is a simple test.', 'This is a simple test case', 21, '.'],
            ['The quick brown fox jumps over the lazy dog', 'The quick brown fox jumps over the lazy dog', 255, '…'],
            ['The lazy fox jumps over the quick brown dog', 'The lazy fox jumps over the quick brown dog', 0, '…'],
            ['This_is_a_simple_test_ca…', 'This_is_a_simple_test_case', 24, '…'],
        ];
    }

    public function testTextHelper()
    {
        $this->assertInstanceOf('\Concrete\Core\Utility\Service\Text', Core::make('helper/text'));
    }

    /**
     * @dataProvider asciifyDataProvider
     *
     * @param mixed $expected
     * @param mixed $input1
     * @param mixed $input2
     */
    public function testAsciify($expected, $input1, $input2)
    {
        $this->assertEquals($expected, $this->object->asciify($input1, $input2));
    }

    /**
     * @dataProvider urlifyDataProvider
     *
     * @param mixed $expected
     * @param mixed $input
     */
    public function testUrlify($expected, $input)
    {
        $this->assertEquals($expected, $this->object->urlify($input));
    }

    /**
     * Test for many rounds with a language, that has no map associated
     * This causes a "regular expression is too large" error on old versions.
     */
    public function testUrlify_regexTooLarge()
    {
        for ($i = 0; $i < 1000; ++$i) {
            $this->object->urlify('Lo siento, no hablo español.', 60, -1);
        }
    }

    /**
     * @dataProvider shortenDataProvider
     *
     * @param mixed $expected
     * @param mixed $input1
     * @param mixed $input2
     * @param mixed $input3
     */
    public function testShortenTextWord($expected, $input1, $input2, $input3)
    {
        $this->assertEquals($expected, $this->object->shortenTextWord($input1, $input2, $input3));
    }

    /**
     * @dataProvider shortenDataProvider
     *
     * @param mixed $expected
     * @param mixed $input1
     * @param mixed $input2
     * @param mixed $input3
     */
    public function testWordSafeShortText($expected, $input1, $input2, $input3)
    {
        $this->assertEquals($expected, $this->object->wordSafeShortText($input1, $input2, $input3));
    }

    public function autolinkDataProvider()
    {
        return [
            ['', ''],
            ['This is not a link', 'This is not a link'],
            ['<a href="http://www.concrete5.org" rel="nofollow">www.concrete5.org</a>', 'www.concrete5.org'],
            ['<a href="http://www.concrete5.org" target="_blank" rel="nofollow">www.concrete5.org</a>', 'www.concrete5.org', true],
            ['Before <a href="http://www.concrete5.org" rel="nofollow">www.concrete5.org</a> after', 'Before www.concrete5.org after'],
            ['<a href="http://concrete5.org" rel="nofollow">concrete5.org</a>', 'http://concrete5.org'],
            ['<a href="https://concrete5.org" rel="nofollow">concrete5.org</a>', 'https://concrete5.org'],
            ['Before <a href="http://concrete5.org" rel="nofollow">concrete5.org</a> after', 'Before http://concrete5.org after'],
            ['Before <a href="https://concrete5.org" rel="nofollow">concrete5.org</a> after', 'Before https://concrete5.org after'],
            ['<a href="http://concrete5.org" rel="nofollow">concrete5.org</a> <a href="https://concrete5.org" rel="nofollow">concrete5.org</a>', 'http://concrete5.org https://concrete5.org'],
            ['<a href="http://www.concrete5.org" rel="nofollow">www.concrete5.org</a> <a href="https://concrete5.org" rel="nofollow">concrete5.org</a>', 'www.concrete5.org https://concrete5.org'],
            ['<a href="https://www.concrete5.org" rel="nofollow">www.concrete5.org</a>', 'www.concrete5.org', false, 'https://'],
        ];
    }

    /**
     * @dataProvider autolinkDataProvider
     *
     * @param mixed $expected
     * @param mixed $input
     * @param mixed $newWindow
     * @param mixed $defaultProtocol
     */
    public function testAutolink($expected, $input, $newWindow = false, $defaultProtocol = 'http://')
    {
        $this->assertSame($expected, $this->object->autolink($input, $newWindow, $defaultProtocol));
    }
}
