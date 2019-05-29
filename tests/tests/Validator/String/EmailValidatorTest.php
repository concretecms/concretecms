<?php

namespace Concrete\Tests\Validator\String;

use PHPUnit_Framework_TestCase;

class EmailValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testIsValid()
    {
        $validator = new \Concrete\Core\Validator\String\EmailValidator(false, false);

        $this->assertNotEmpty($validator->getRequirementStrings());

        $this->assertFalse($validator->isValid(null));
        $this->assertFalse($validator->isValid(''));
        $this->assertFalse($validator->isValid('x'));
        $this->assertFalse($validator->isValid('example@'));
        $this->assertFalse($validator->isValid('@example.com'));
        $this->assertTrue($validator->isValid('someone@example.com'));
    }

    public function testErrorAdded()
    {
        $validator = new \Concrete\Core\Validator\String\EmailValidator();

        $this->assertFalse($validator->isValid('x', $error = new \ArrayObject()));
        $this->assertNotEmpty($error);
    }

    public function testInvalidInput()
    {
        $validator = new \Concrete\Core\Validator\String\EmailValidator();

        $this->setExpectedException('Exception');
        $validator->isValid($validator);
    }
}
