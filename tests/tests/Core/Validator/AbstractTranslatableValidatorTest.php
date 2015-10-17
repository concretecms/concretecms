<?php
namespace tests\Core\Validator;

class AbstractTranslatableValidatorTest extends \PHPUnit_Framework_TestCase
{

    protected function getMockValidator(\Closure $validation_closure)
    {
        // Mock the abstract translatable validator
        $mock = $this->getMockForAbstractClass('\Concrete\Core\Validator\AbstractTranslatableValidator');

        // Rebind closure to $mock's context
        $closure = $validation_closure->bindTo($mock, $mock);

        // Stub method
        $mock->method('isValid')->will($this->returnCallback($closure));

        return $mock;
    }

    public function testClosureMessage()
    {
        $test_code = 5;
        $test_string = 'test';

        $called = false;

        $obj = $this;

        $mock = $this->getMockValidator(function ($mixed) use ($obj) {
            $obj->assertEquals('CLOSURE', $this->getErrorString(5, $mixed));
            $obj->assertEquals('default', $this->getErrorString(1, $mixed, 'default'));
        });

        /** @type \Concrete\Core\Validator\AbstractTranslatableValidator $mock */
        $mock->setErrorString(5, function ($validator, $code, $passed) use ($mock, $test_code, $test_string, &$called) {
            $this->assertEquals($mock, $validator);
            $this->assertEquals($code, $test_code);
            $this->assertEquals($passed, $test_string);

            $called = true;

            return 'CLOSURE';
        });

        $mock->isValid($test_string);
        $this->assertTrue($called);
    }

    public function testStringMessage()
    {
        $obj = $this;
        $called = false;
        $mock = $this->getMockValidator(function($mixed) use ($obj, &$called) {
            $obj->assertEquals('ERROR', $this->getErrorString(5, $mixed));
            $called = true;
        });

        /** @type \Concrete\Core\Validator\AbstractTranslatableValidator $mock */
        $mock->setErrorString(5, 'ERROR');

        $mock->isValid('false');
        $this->assertTrue($called);
    }

    public function testClosureRequirement()
    {
        $test_code = 5;
        $called = false;

        $mock = $this->getMockValidator(function() {

        });

        /** @type \Concrete\Core\Validator\AbstractTranslatableValidator $mock */
        $mock->setRequirementString(5, function ($validator, $code) use ($mock, $test_code, &$called) {
            $this->assertEquals($mock, $validator);
            $this->assertEquals($code, $test_code);

            $called = true;

            return 'REQUIREMENT';
        });

        $this->assertEquals(array(5 => 'REQUIREMENT'), $mock->getRequirementStrings());
        $this->assertTrue($called);
    }

    public function testStringRequirement()
    {
        $mock = $this->getMockValidator(function(){});

        /** @type \Concrete\Core\Validator\AbstractTranslatableValidator $mock */
        $mock->setRequirementString(5, 'REQUIREMENT');
        $this->assertEquals(array(5 => 'REQUIREMENT'), $mock->getRequirementStrings());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidErrorStringExpression()
    {
        $mock = $this->getMockValidator(function(){});
        $mock->setErrorString(5, $mock);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidRequirementStringExpression()
    {
        $mock = $this->getMockValidator(function(){});
        $mock->setErrorString(5, $mock);
    }

}
