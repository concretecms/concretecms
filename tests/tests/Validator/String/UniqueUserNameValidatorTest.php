<?php

namespace Concrete\Tests\Validator\String;

use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;

class UniqueUserNameValidatorTest extends ConcreteDatabaseTestCase
{
    protected $fixtures = [
        'UniqueUserData',
    ];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
            \Concrete\Core\Entity\User\User::class
        ]);
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
