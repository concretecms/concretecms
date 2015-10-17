<?php
namespace Concrete\Core\Tests\Validator\String;

class MinimumLengthValidatorTest extends \PHPUnit_Framework_TestCase
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
        $error = $this->getMock('Concrete\Core\Error\Error');
        $error->expects($this->once())->method('add');

        $this->assertFalse($validator->isValid('1234', $error));
    }

    public function testInvalidInput()
    {
        $validator = new \Concrete\Core\Validator\String\MinimumLengthValidator(5);

        $this->setExpectedException('InvalidArgumentException');
        $validator->isValid($validator);
    }

}
