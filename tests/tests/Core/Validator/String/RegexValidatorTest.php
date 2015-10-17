<?php
namespace Concrete\Core\Tests\Validator\String;

class RegexValidatorTest extends \PHPUnit_Framework_TestCase
{

    public function testIsValid()
    {
        $validator = new \Concrete\Core\Validator\String\RegexValidator('//');
        $validator->setPattern('/(pass)/');

        $this->assertNotEmpty($validator->getRequirementStrings());

        $this->assertFalse($validator->isValid('fail'));
        $this->assertTrue($validator->isValid('pass'));
        $this->assertFalse($validator->isValid('this should still fail'));
        $this->assertTrue($validator->isValid('this should still pass'));
    }

    public function testErrorAdded()
    {
        $validator = new \Concrete\Core\Validator\String\RegexValidator('/test/');
        $error = $this->getMock('Concrete\Core\Error\Error');
        $error->expects($this->once())->method('add');

        $this->assertFalse($validator->isValid('123456', $error));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidInput()
    {
        $validator = new \Concrete\Core\Validator\String\RegexValidator('');

        $validator->isValid($validator);

        $this->setExpectedException('RuntimeException');
        $validator->isValid('');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testInvalidRegularExpression()
    {
        $validator = new \Concrete\Core\Validator\String\RegexValidator('Invalid regex');
        $validator->isValid('test');
    }

}
