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
        /** @var \Concrete\Core\Validator\String\UniqueUserNameValidator  $validator */
        $validator = \Core::make(\Concrete\Core\Validator\String\UniqueUserNameValidator::class);

        static::assertNotEmpty($validator->getRequirementStrings());

        static::assertTrue($validator->isValidFor('new_user'));
        static::assertFalse($validator->isValid('admin'));
        static::assertFalse($validator->isValidFor('admin'));
        static::assertTrue($validator->isValidFor('admin', 1));
        static::assertFalse($validator->isValidFor('admin', 2));
    }

    public function testErrorAdded()
    {
        $validator = \Core::make(\Concrete\Core\Validator\String\UniqueUserNameValidator::class);

        $this->assertFalse($validator->isValid('admin', $error = new \ArrayObject()));
        $this->assertNotEmpty($error);
    }

    public function testInvalidInput()
    {
        $this->expectException(\Exception::class);
        $validator = \Core::make(\Concrete\Core\Validator\String\UniqueUserNameValidator::class);
        $validator->isValid($validator);
    }
}
