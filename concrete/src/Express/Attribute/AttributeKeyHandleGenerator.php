<?php
namespace Concrete\Core\Express\Attribute;

use Concrete\Core\Attribute\AttributeKeyHandleGeneratorInterface;
use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Attribute\Key\Key;

class AttributeKeyHandleGenerator implements AttributeKeyHandleGeneratorInterface
{

    protected $category;

    public function __construct(ExpressCategory $category)
    {
        $this->category = $category;
    }

    protected function handleIsAvailable($handleToTest, Key $existingKey)
    {
        $key = $this->category->getByHandle($handleToTest);
        if (is_object($key)) {
            if ($key->getAttributeKeyID() != $existingKey->getAttributeKeyID()) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * @param ExpressKey $key
     * @return string
     */
    public function generate(Key $key)
    {
        $name = $key->getAttributeKeyName();

        $text = \Core::make('helper/text');
        $handle = $text->handle($name);
        if (!$handle) {
            $handle = 'attribute_key';
        }
        $baseHandle = substr($handle, 0, 42);

        if ($this->handleIsAvailable($baseHandle, $key)) {
            return $baseHandle;
        }
        $suffix = 2;
        $handle = $baseHandle . '_' . $suffix;
        while (!$this->handleIsAvailable($handle, $key)) {
            $suffix++;
            $handle = $baseHandle . '_' . $suffix;
        }

        return $handle;
    }

}
