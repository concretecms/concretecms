<?php

namespace Concrete\Tests\Attribute\Type;

use Concrete\TestHelpers\Attribute\AttributeTypeTestCase;
use Concrete\Core\Error\ErrorList\Error\FieldNotPresentError;

class ImageFileTest extends AttributeTypeTestCase
{
    protected $atHandle = 'image_file';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
            'Concrete\Core\Entity\Attribute\Key\Settings\ImageFileSettings'
        ]);
    }

    public function testValidateFormEmptyArray(): void
    {
        $this->assertInstanceOf(FieldNotPresentError::class, $this->ctrl->validateForm(null));
    }
}
