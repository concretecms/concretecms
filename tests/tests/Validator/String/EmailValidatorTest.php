<?php

namespace Concrete\Tests\Validator\String;

use Concrete\Tests\TestCase;

class EmailValidatorTest extends TestCase
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
        $this->assertFalse($validator->isValid('someone@example.com🍛'));
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
        $this->expectException(\Exception::class);
        $validator = new \Concrete\Core\Validator\String\EmailValidator();
        $validator->isValid($validator);
    }
}
