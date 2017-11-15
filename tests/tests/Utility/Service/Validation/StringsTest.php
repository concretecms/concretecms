<?php

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
        $this->object = new \Concrete\Core\Utility\Service\Validation\Strings();
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
        return array(
            //no mx record validation
            array(false, '', false),
            array(false, 'Mmmmm Cookies', false),
            array(false, 'example.com', false),
            array(false, 'notvalid@', false),
            array(false, 'A@b@c@example.com', false),
            array(false, 'a"b(c)d,e:f;g<h>i[j\k]l@example.com', false),
            array(false, 'just"not"right@example.com', false),
            array(false, 'this is"not\allowed@example.com', false),
            array(false, 'this\ still\"not\\allowed@example.com', false),
            array(false, 'notvalid..@example.com', false),
            array(false, 'notvalid@..example.com', false),
            array(true, 'is+valid@concrete5.org', false),
            array(true, 'tld@international', false),
            array(true, 'tld@international', false),
            array(true, 'international@international', false),
            array(true, 'a.little.lengthy.but.fine@dept.example.com', false),
            array(true, 'other.email-with-dash@example.com', false),
            array(true, 'test@concrete5.org', false),

            //mx validation
            array(true, 'test@concrete5.org', true),
        );
    }

    public function alphanumDataProvider()
    {
        return array(
            array(false, null, false, false),
            array(false, true, false, false),
            array(false, false, false, false),
            array(false, 1.254, false, false),
            array(false, '', false, false),
            array(false, ' ', false, false),
            array(false, ' ', true, false),
            array(false, 'a  b--c', false, true),
            array(false, 1, false, false),
            array(false, 0, false, false),
            array(false, array('testing'), false, false),
            array(true, 'JustALongerStringForABasicTest', false, false),
            array(true, 'Just A Longer String For A Basic Test', true, false),
            array(true, 'Just-A-Longer-String-For-A-Basic-Test', false, true),
            array(true, 'a ', true, false),
            array(true, ' -', true, true),
            array(true, 'a  b--c', true, true),
            array(true, '1', false, false),
            array(true, '0', false, false),
        );
    }

    public function handleDataProvider()
    {
        return array(
            array(false, null),
            array(false, true),
            array(false, false),
            array(false, 1.254),
            array(false, ''),
            array(false, ' '),
            array(false, 'a  b--c'),
            array(false, 1),
            array(false, 0),
            array(false, 'Just A Longer String For A Basic Test'),
            array(false, 'Just-A-Longer-String-For-A-Basic-Test'),
            array(false, array('testing')),
            array(true, 'JustALongerStringForABasicTest12'),
            array(true, 'Just_A_Longer_String_For_1_Basic_Test2'),
            array(true, '_1'),
            array(true, '___'),
        );
    }

    public function notEmptyDataProvider()
    {
        return array(
            array(false, null),
            array(false, true),
            array(false, false),
            array(false, 1.254),
            array(false, ''),
            array(false, ' '),
            array(false, '     '),
            array(false, 1),
            array(false, 0),
            array(false, array()),
            array(false, new stdClass()),
            array(false, array('testing')),
            array(false, array()),
            array(true, 'a  b--c'),
            array(true, ' Just A Longer String For A Basic Test '),
        );
    }

    public function minDataProvider()
    {
        return array(
            array(false, null, 1),
            array(false, true, 0),
            array(false, false, 0),
            array(false, 1.254, 1),
            array(false, '', 0),
            array(false, ' ', 0),
            array(false, '     ', 0),
            array(false, 't', 2),
            array(false, ' t e ', 4),
            array(false, array(), 1),
            array(false, new stdClass(), 1),
            array(true, ' super duper ', 11),
            array(true, 'super duper ', 1),
        );
    }

    public function maxDataProvider()
    {
        return array(
            array(false, null, 1),
            array(false, true, 0),
            array(false, false, 0),
            array(false, 1.254, 1),
            array(false, '', 0),
            array(false, ' ', 0),
            array(false, '     ', 0),
            array(false, 'fail test', 2),
            array(false, ' t e ', 2),
            array(false, array(), 1),
            array(false, new stdClass(), 1),
            array(false, ' super duper ', 10),
            array(true, ' super duper ', 11),
            array(true, 'super duper', 11),
        );
    }

    public function containsNumberDataProvider()
    {
        return array(
            array(0, null),
            array(0, true),
            array(0, false),
            array(0, 1.254),
            array(0, ''),
            array(0, ' '),
            array(0, ' t e '),
            array(0, array()),
            array(0, new stdClass()),
            array(1, ' super_duper 1 '),
            array(2, ' 2super_duper1 '),
            array(18, '123456789abcdefghijklmnopqrstuvwxyz987654321'),
        );
    }

    public function containsUpperCaseDataProvider()
    {
        return array(
            array(0, null),
            array(0, true),
            array(0, false),
            array(0, 1.254),
            array(0, ''),
            array(0, ' '),
            array(0, ' t e '),
            array(0, array()),
            array(0, new stdClass()),
            array(1, ' Super_duper 1 '),
            array(2, ' 2Super_Duper1 '),
            array(13, 'AbCdEfGhIjKlMnOpQrStUvWxYz123'),
        );
    }

    public function containsLowerCaseDataProvider()
    {
        return array(
            array(0, null),
            array(0, true),
            array(0, false),
            array(0, 1.254),
            array(0, ''),
            array(0, ' '),
            array(2, ' t e '),
            array(0, array()),
            array(0, new stdClass()),
            array(0, ' SUPER_DUPER 1 '),
            array(8, ' 2Super_Duper1 '),
            array(13, 'AbCdEfGhIjKlMnOpQrStUvWxYz123'),
        );
    }

    public function containsSymbolDataProvider()
    {
        return array(
            array(0, null),
            array(0, true),
            array(0, false),
            array(0, 1.254),
            array(0, ''),
            array(0, ' '),
            array(0, ' t e '),
            array(0, array()),
            array(0, new stdClass()),
            array(1, ' SUPER_DUPER 1 '),
            array(3, ' !2Super Duper1@ '),
            array(29, '!@#$%^&*()_-+=[]{};:\'"\\|?.,<>'),
        );
    }

    /**
     * @dataProvider emailDataProvider
     */
    public function testEmail($expected, $email, $mxValidation)
    {
        $this->assertEquals($expected, $this->object->email($email, $mxValidation));
    }

    /**
     * @dataProvider alphanumDataProvider
     */
    public function testAlphaNum($expected, $value, $allowSpaces = false, $allowDashes = false)
    {
        $this->assertEquals($expected, $this->object->alphanum($value, $allowSpaces, $allowDashes));
    }

    /**
     * @dataProvider handleDataProvider
     */
    public function testHandle($expected, $value)
    {
        $this->assertEquals($expected, $this->object->handle($value));
    }

    /**
     * @dataProvider notEmptyDataProvider
     */
    public function testNotEmpty($expected, $value)
    {
        $this->assertEquals($expected, $this->object->notempty($value));
    }

    /**
     * @dataProvider minDataProvider
     */
    public function testMin($expected, $string, $minLength)
    {
        $this->assertEquals($expected, $this->object->min($string, $minLength));
    }

    /**
     * @dataProvider maxDataProvider
     */
    public function testMax($expected, $string, $maxLength)
    {
        $this->assertEquals($expected, $this->object->max($string, $maxLength));
    }

    /**
     * @dataProvider containsNumberDataProvider
     */
    public function testContainsNumber($expected, $string)
    {
        $this->assertEquals($expected, $this->object->containsNumber($string));
    }

    /**
     * @dataProvider containsUpperCaseDataProvider
     */
    public function testContainsUpperCase($expected, $string)
    {
        $this->assertEquals($expected, $this->object->containsUpperCase($string));
    }

    /**
     * @dataProvider containsLowerCaseDataProvider
     */
    public function testContainsLowerCase($expected, $string)
    {
        $this->assertEquals($expected, $this->object->containsLowerCase($string));
    }

    /**
     * @dataProvider containsSymbolDataProvider
     */
    public function testContainsSymbol($expected, $string)
    {
        $this->assertEquals($expected, $this->object->containsSymbol($string));
    }
}
