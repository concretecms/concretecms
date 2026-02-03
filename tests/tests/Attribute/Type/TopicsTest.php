<?php

namespace Concrete\Tests\Attribute\Type;

use Concrete\TestHelpers\Attribute\AttributeTypeTestCase;

class TopicsTest extends AttributeTypeTestCase
{
    protected $atHandle = 'topics';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
            'Concrete\Core\Entity\Attribute\Key\Settings\TopicsSettings',
        ]);
    }

    public function testValidateFormEmptyArray(): void
    {
        $this->assertFalse($this->ctrl->validateForm(null));
    }
}
