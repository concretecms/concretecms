<?php

namespace Concrete\Tests\Validator\String;

use Concrete\Tests\TestCase;

class MaximumLengthValidatorTest extends TestCase
{
    public function testIsValid()
    {
        $validator = new \Concrete\Core\Validator\String\MaximumLengthValidator(1000);
        $validator->setMaximumLength(5);

        $this->assertNotEmpty($validator->getRequirementStrings());

        $this->assertFalse($validator->isValid('123456'));
        $this->assertTrue($validator->isValid('12345'));
        $this->assertFalse($validator->isValid('12345678'));
        $this->assertTrue($validator->isValid('12'));
    }

    public function testErrorAdded()
    {
        $validator = new \Concrete\Core\Validator\String\MaximumLengthValidator(5);

        $this->assertFalse($validator->isValid('123456', $error = new \ArrayObject()));
        $this->assertNotEmpty($error);
    }

    public function testInvalidInput()
    {
        $this->expectException(\InvalidArgumentException::class);
        $validator = new \Concrete\Core\Validator\String\MaximumLengthValidator(5);
        $validator->isValid($validator);
    }
}
