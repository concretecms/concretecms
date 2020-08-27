<?php

namespace Concrete\Core\Attribute\Key\Component\KeySelector;

use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\ObjectInterface;

/**
 * Responsible for retrieving attribute values from objects and serializing them.
 */
class ObjectsSerializer implements \JsonSerializable
{
    /**
     * @var CategoryInterface
     */
    protected $category;

    /**
     * @var ObjectInterface[]
     */
    protected $objects;

    /**
     * ObjectsSerializer constructor.
     *
     * @param CategoryInterface $category
     * @param ObjectInterface[] $objects
     */
    public function __construct(CategoryInterface $category, array $objects)
    {
        $this->category = $category;
        $this->objects = $objects;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $aks = [];
        foreach ($this->objects as $objIdx => $object) {
            $values = $this->category->getAttributeValues($object);
            foreach ($values as $value) {
                $ak = $value->getAttributeKey();
                if (!isset($aks[$ak->getAttributeKeyID()])) {
                    $aks[$ak->getAttributeKeyID()] = array_fill(0, count($this->objects), null);
                }

                $aks[$ak->getAttributeKeyID()][$objIdx] = $value;
            }
        }

        $data = [];
        foreach ($aks as $akID => $values) {
            if (count(array_unique($values)) === 1) {
                $value = head($values);
                $data[] = new KeySerializer($value->getAttributeKey(), $value);
            } else {
                $value = current(array_filter($values));
                $data[] = new KeySerializer($value->getAttributeKey(), null);
            }
        }

        return $data;
    }
}
