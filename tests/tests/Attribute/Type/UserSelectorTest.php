<?php

namespace Concrete\Tests\Attribute\Type;

use Concrete\TestHelpers\Attribute\AttributeTypeTestCase;

class UserSelectorTest extends AttributeTypeTestCase
{
    protected $atHandle = 'user_selector';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
            'Concrete\Core\Entity\Attribute\Key\Settings\EmptySettings',
        ]);
    }

    public function testValidateFormEmptyArray(): void
    {
        $this->assertFalse($this->ctrl->validateForm(null));
    }
}
