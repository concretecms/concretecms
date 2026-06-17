<?php

namespace Concrete\Tests\Validator\String;

use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;

class UniqueUserEmailValidatorTest extends ConcreteDatabaseTestCase
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
        $this->expectException(\Exception::class);
        $validator = \Core::make(\Concrete\Core\Validator\String\UniqueUserEmailValidator::class);
        $validator->isValid($validator);
    }
}
