<?php

namespace Concrete\Tests\Attribute\Type;

use Concrete\TestHelpers\Attribute\AttributeTypeTestCase;
use Concrete\Core\Error\ErrorList\Error\FieldNotPresentError;

class EmailTest extends AttributeTypeTestCase
{
    protected $atHandle = 'email';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
            'Concrete\Core\Entity\Attribute\Key\Settings\TextSettings'
        ]);
    }

    public function testValidateFormEmptyArray(): void
    {
        $this->assertInstanceOf(FieldNotPresentError::class, $this->ctrl->validateForm(null));
    }
}
