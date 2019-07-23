<?php

namespace Concrete\Tests\Utility\Service\Validation;

use PHPUnit_Framework_TestCase;
use stdClass;

class StringsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Concrete\Core\Utility\Service\Validation\Strings
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = \Core::make(\Concrete\Core\Utility\Service\Validation\Strings::class);
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

    public function emailDataProvider()
    {
        return [
            //no mx record validation
            [false, '', false],
            [false, 'Mmmmm Cookies', false],
            [false, 'example.com', false],
            [false, 'notvalid@', false],
            [false, 'A@b@c@example.com', false],
            [false, 'a"b(c)d,e:f;g<h>i[j\k]l@example.com', false],
            [false, 'just"not"right@example.com', false],
            [false, 'this is"not\allowed@example.com', false],
            [false, 'this\ still\"not\\allowed@example.com', false],
            [false, 'notvalid..@example.com', false],
            [false, 'notvalid@..example.com', false],
            [true, 'is+valid@concrete5.org', false],
            [true, 'tld@international', false],
            [true, 'tld@international', false],
            [true, 'international@international', false],
            [true, 'a.little.lengthy.but.fine@dept.example.com', false],
            [true, 'other.email-with-dash@example.com', false],
            [true, 'test@concrete5.org', false],

            //mx validation
            [true, 'test@concrete5.org', true],
        ];
    }

    public function alphanumDataProvider()
    {
        return [
            [false, null, false, false],
            [false, true, false, false],
            [false, false, false, false],
            [false, 1.254, false, false],
            [false, '', false, false],
            [false, ' ', false, false],
            [false, ' ', true, false],
            [false, 'a  b--c', false, true],
            [false, 1, false, false],
            [false, 0, false, false],
            [false, ['testing'], false, false],
            [true, 'JustALongerStringForABasicTest', false, false],
            [true, 'Just A Longer String For A Basic Test', true, false],
            [true, 'Just-A-Longer-String-For-A-Basic-Test', false, true],
            [true, 'a ', true, false],
            [true, ' -', true, true],
            [true, 'a  b--c', true, true],
            [true, '1', false, false],
            [true, '0', false, false],
        ];
    }

    public function handleDataProvider()
    {
        return [
            [false, null],
            [false, true],
            [false, false],
            [false, 1.254],
            [false, ''],
            [false, ' '],
            [false, 'a  b--c'],
            [false, 1],
            [false, 0],
            [false, 'Just A Longer String For A Basic Test'],
            [false, 'Just-A-Longer-String-For-A-Basic-Test'],
            [false, ['testing']],
            [true, 'JustALongerStringForABasicTest12'],
            [true, 'Just_A_Longer_String_For_1_Basic_Test2'],
            [true, '_1'],
            [true, '___'],
        ];
    }

    public function notEmptyDataProvider()
    {
        return [
            [false, null],
            [false, true],
            [false, false],
            [false, 1.254],
            [false, ''],
            [false, ' '],
            [false, '     '],
            [false, 1],
            [false, 0],
            [false, []],
            [false, new stdClass()],
            [false, ['testing']],
            [false, []],
            [true, 'a  b--c'],
            [true, ' Just A Longer String For A Basic Test '],
        ];
    }

    public function minDataProvider()
    {
        return [
            [false, null, 1],
            [false, true, 0],
            [false, false, 0],
            [false, 1.254, 1],
            [false, '', 0],
            [false, ' ', 0],
            [false, '     ', 0],
            [false, 't', 2],
            [false, ' t e ', 4],
            [false, [], 1],
            [false, new stdClass(), 1],
            [true, ' super duper ', 11],
            [true, 'super duper ', 1],
        ];
    }

    public function maxDataProvider()
    {
        return [
            [false, null, 1],
            [false, true, 0],
            [false, false, 0],
            [false, 1.254, 1],
            [false, '', 0],
            [false, ' ', 0],
            [false, '     ', 0],
            [false, 'fail test', 2],
            [false, ' t e ', 2],
            [false, [], 1],
            [false, new stdClass(), 1],
            [false, ' super duper ', 10],
            [true, ' super duper ', 11],
            [true, 'super duper', 11],
        ];
    }

    public function containsNumberDataProvider()
    {
        return [
            [0, null],
            [0, true],
            [0, false],
            [0, 1.254],
            [0, ''],
            [0, ' '],
            [0, ' t e '],
            [0, []],
            [0, new stdClass()],
            [1, ' super_duper 1 '],
            [2, ' 2super_duper1 '],
            [18, '123456789abcdefghijklmnopqrstuvwxyz987654321'],
        ];
    }

    public function containsUpperCaseDataProvider()
    {
        return [
            [0, null],
            [0, true],
            [0, false],
            [0, 1.254],
            [0, ''],
            [0, ' '],
            [0, ' t e '],
            [0, []],
            [0, new stdClass()],
            [1, ' Super_duper 1 '],
            [2, ' 2Super_Duper1 '],
            [13, 'AbCdEfGhIjKlMnOpQrStUvWxYz123'],
        ];
    }

    public function containsLowerCaseDataProvider()
    {
        return [
            [0, null],
            [0, true],
            [0, false],
            [0, 1.254],
            [0, ''],
            [0, ' '],
            [2, ' t e '],
            [0, []],
            [0, new stdClass()],
            [0, ' SUPER_DUPER 1 '],
            [8, ' 2Super_Duper1 '],
            [13, 'AbCdEfGhIjKlMnOpQrStUvWxYz123'],
        ];
    }

    public function containsSymbolDataProvider()
    {
        return [
            [0, null],
            [0, true],
            [0, false],
            [0, 1.254],
            [0, ''],
            [0, ' '],
            [0, ' t e '],
            [0, []],
            [0, new stdClass()],
            [1, ' SUPER_DUPER 1 '],
            [3, ' !2Super Duper1@ '],
            [29, '!@#$%^&*()_-+=[]{};:\'"\\|?.,<>'],
        ];
    }

    /**
     * @dataProvider emailDataProvider
     *
     * @param mixed $expected
     * @param mixed $email
     * @param mixed $mxValidation
     */
    public function testEmail($expected, $email, $mxValidation)
    {
        $this->assertEquals($expected, $this->object->email($email, $mxValidation));
    }

    /**
     * @dataProvider alphanumDataProvider
     *
     * @param mixed $expected
     * @param mixed $value
     * @param mixed $allowSpaces
     * @param mixed $allowDashes
     */
    public function testAlphaNum($expected, $value, $allowSpaces = false, $allowDashes = false)
    {
        $this->assertEquals($expected, $this->object->alphanum($value, $allowSpaces, $allowDashes));
    }

    /**
     * @dataProvider handleDataProvider
     *
     * @param mixed $expected
     * @param mixed $value
     */
    public function testHandle($expected, $value)
    {
        $this->assertEquals($expected, $this->object->handle($value));
    }

    /**
     * @dataProvider notEmptyDataProvider
     *
     * @param mixed $expected
     * @param mixed $value
     */
    public function testNotEmpty($expected, $value)
    {
        $this->assertEquals($expected, $this->object->notempty($value));
    }

    /**
     * @dataProvider minDataProvider
     *
     * @param mixed $expected
     * @param mixed $string
     * @param mixed $minLength
     */
    public function testMin($expected, $string, $minLength)
    {
        $this->assertEquals($expected, $this->object->min($string, $minLength));
    }

    /**
     * @dataProvider maxDataProvider
     *
     * @param mixed $expected
     * @param mixed $string
     * @param mixed $maxLength
     */
    public function testMax($expected, $string, $maxLength)
    {
        $this->assertEquals($expected, $this->object->max($string, $maxLength));
    }

    /**
     * @dataProvider containsNumberDataProvider
     *
     * @param mixed $expected
     * @param mixed $string
     */
    public function testContainsNumber($expected, $string)
    {
        $this->assertEquals($expected, $this->object->containsNumber($string));
    }

    /**
     * @dataProvider containsUpperCaseDataProvider
     *
     * @param mixed $expected
     * @param mixed $string
     */
    public function testContainsUpperCase($expected, $string)
    {
        $this->assertEquals($expected, $this->object->containsUpperCase($string));
    }

    /**
     * @dataProvider containsLowerCaseDataProvider
     *
     * @param mixed $expected
     * @param mixed $string
     */
    public function testContainsLowerCase($expected, $string)
    {
        $this->assertEquals($expected, $this->object->containsLowerCase($string));
    }

    /**
     * @dataProvider containsSymbolDataProvider
     *
     * @param mixed $expected
     * @param mixed $string
     */
    public function testContainsSymbol($expected, $string)
    {
        $this->assertEquals($expected, $this->object->containsSymbol($string));
    }
}
