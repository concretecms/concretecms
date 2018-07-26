<?php

namespace Concrete\Tests\Validator\String;

use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;

class UniqueUserEmailValidatorTest extends ConcreteDatabaseTestCase
{
    protected $fixtures = [
        'UniqueUserData',
    ];

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->metadatas[] = \Concrete\Core\Entity\User\User::class;
    }

    public function testIsValid()
    {
        $validator = \Core::make(\Concrete\Core\Validator\String\UniqueUserEmailValidator::class);

        $this->assertNotEmpty($validator->getRequirementStrings());

        $this->assertFalse($validator->isValid(null));
        $this->assertFalse($validator->isValid(''));
        $this->assertFalse($validator->isValid('x'));
        $this->assertFalse($validator->isValid('example@'));
        $this->assertFalse($validator->isValid('@example.com'));
        $this->assertTrue($validator->isValid('someone@example.com'));
        $this->assertFalse($validator->isValidFor('master@example.com'));
        $this->assertTrue($validator->isValidFor('master@example.com', 1));
        $this->assertFalse($validator->isValidFor('master@example.com', 2));
    }

    public function testErrorAdded()
    {
        $validator = \Core::make(\Concrete\Core\Validator\String\UniqueUserEmailValidator::class);

        $this->assertFalse($validator->isValid('x', $error = new \ArrayObject()));
        $this->assertNotEmpty($error);
    }

    public function testInvalidInput()
    {
        $validator = \Core::make(\Concrete\Core\Validator\String\UniqueUserEmailValidator::class);

        $this->setExpectedException('Exception');
        $validator->isValid($validator);
    }
}
