<?php

namespace Concrete\Tests\Validator\String;

use Concrete\Tests\TestCase;

class MinimumLengthValidatorTest extends TestCase
{
    public function testIsValid()
    {
        $validator = new \Concrete\Core\Validator\String\MinimumLengthValidator(0);
        $validator->setMinimumLength(5);

        $this->assertNotEmpty($validator->getRequirementStrings());

        $this->assertFalse($validator->isValid('1234'));
        $this->assertTrue($validator->isValid('12345'));
        $this->assertFalse($validator->isValid('123'));
        $this->assertTrue($validator->isValid('123456'));
    }

    public function testErrorAdded()
    {
        $validator = new \Concrete\Core\Validator\String\MinimumLengthValidator(5);

        $this->assertFalse($validator->isValid('1234', $error = new \ArrayObject()));
        $this->assertNotEmpty($error);
    }

    public function testInvalidInput()
    {
        $this->expectException(\InvalidArgumentException::class);
        $validator = new \Concrete\Core\Validator\String\MinimumLengthValidator(5);
        $validator->isValid($validator);
    }
}
