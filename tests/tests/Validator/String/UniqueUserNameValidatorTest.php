<?php

namespace Concrete\Tests\Validator\String;

use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;

class UniqueUserNameValidatorTest extends ConcreteDatabaseTestCase
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
        $validator = \Core::make(\Concrete\Core\Validator\String\UniqueUserNameValidator::class);

        $this->assertNotEmpty($validator->getRequirementStrings());

        $this->assertTrue($validator->isValidFor('new_user'));
        $this->assertFalse($validator->isValid('admin'));
        $this->assertFalse($validator->isValidFor('admin'));
        $this->assertTrue($validator->isValidFor('admin', 1));
        $this->assertFalse($validator->isValidFor('admin', 2));
    }

    public function testErrorAdded()
    {
        $validator = \Core::make(\Concrete\Core\Validator\String\UniqueUserNameValidator::class);

        $this->assertFalse($validator->isValid('admin', $error = new \ArrayObject()));
        $this->assertNotEmpty($error);
    }

    public function testInvalidInput()
    {
        $validator = \Core::make(\Concrete\Core\Validator\String\UniqueUserNameValidator::class);

        $this->setExpectedException('Exception');
        $validator->isValid($validator);
    }
}
