<?php
namespace Concrete\Core\Attribute\Key\Component\KeySelector;

use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\ObjectInterface;

/**
 * Responsible for retrieving attribute values from an object and serializing them.
 */
class ObjectSerializer implements \JsonSerializable
{
    /**
     * @var CategoryInterface
     */
    protected $category;

    /**
     * @var ObjectInterface
     */
    protected $object;

    /**
     * ObjectSerializer constructor.
     * @param CategoryInterface $category
     * @param ObjectInterface $object
     */
    public function __construct(CategoryInterface $category, ObjectInterface $object)
    {
        $this->category = $category;
        $this->object = $object;
    }

    /**
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = [];
        $values = $this->category->getAttributeValues($this->object);
        foreach($values as $value) {
            $keySerializer = new KeySerializer($value->getAttributeKey(), $value);
            $data[] = $keySerializer;
        }
        return $data;
    }
}
