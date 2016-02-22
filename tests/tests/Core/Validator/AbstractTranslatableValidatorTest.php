<?php
namespace tests\Core\Validator;

class AbstractTranslatableValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testClosureMessage()
    {
        $obj = $this;

        $test_code = 5;
        $test_string = 'test';

        $mock = $this->getMockForAbstractClass('\Concrete\Core\Validator\AbstractTranslatableValidator');
        $method = new \ReflectionMethod($mock, 'getErrorString');
        $method->setAccessible(true);

        /* @type \Concrete\Core\Validator\AbstractTranslatableValidator $mock */
        $mock->setErrorString(5, function ($validator, $code, $passed) use ($mock, $test_code, $test_string, $obj) {
            $obj->assertEquals($mock, $validator);
            $obj->assertEquals($code, $test_code);
            $obj->assertEquals($passed, $test_string);

            return 'CLOSURE';
        });

        $this->assertEquals('CLOSURE', $method->invokeArgs($mock, array(5, $test_string)));
    }

    public function testStringMessage()
    {
        $mock = $this->getMockForAbstractClass('\Concrete\Core\Validator\AbstractTranslatableValidator');
        $method = new \ReflectionMethod($mock, 'getErrorString');
        $method->setAccessible(true);

        /* @type \Concrete\Core\Validator\AbstractTranslatableValidator $mock */
        $mock->setErrorString(5, 'ERROR');

        $this->assertEquals('ERROR',  $method->invokeArgs($mock, array(5, '')));
        $this->assertEquals('default',  $method->invokeArgs($mock, array(1, '', 'default')));
    }

    public function testClosureRequirement()
    {
        $obj = $this;
        $test_code = 5;
        $mock = $this->getMockForAbstractClass('\Concrete\Core\Validator\AbstractTranslatableValidator');

        /* @type \Concrete\Core\Validator\AbstractTranslatableValidator $mock */
        $mock->setRequirementString(5, function ($validator, $code) use ($mock, $test_code, $obj) {
            $obj->assertEquals($mock, $validator);
            $obj->assertEquals($code, $test_code);

            return 'REQUIREMENT';
        });

        $this->assertEquals(array(5 => 'REQUIREMENT'), $mock->getRequirementStrings());
    }

    public function testStringRequirement()
    {
        $mock = $this->getMockForAbstractClass('\Concrete\Core\Validator\AbstractTranslatableValidator');

        /* @type \Concrete\Core\Validator\AbstractTranslatableValidator $mock */
        $mock->setRequirementString(5, 'REQUIREMENT');
        $this->assertEquals(array(5 => 'REQUIREMENT'), $mock->getRequirementStrings());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidErrorStringExpression()
    {
        $mock = $this->getMockForAbstractClass('\Concrete\Core\Validator\AbstractTranslatableValidator');
        $mock->setErrorString(5, $mock);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidRequirementStringExpression()
    {
        $mock = $this->getMockForAbstractClass('\Concrete\Core\Validator\AbstractTranslatableValidator');
        $mock->setRequirementString(5, $mock);
    }
}
