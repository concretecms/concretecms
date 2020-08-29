<?php

namespace Concrete\Controller\Element\Attribute\Component;

use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Key\Component\KeySelector\ObjectSerializer;
use Concrete\Core\Attribute\Key\Component\KeySelector\ObjectsSerializer;
use Concrete\Core\Attribute\Key\Component\KeySelector\SetSerializer;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Controller\ElementController;

class KeySelector extends ElementController
{
    /**
     * @var string
     */
    protected $selectAttributeUrl;

    /**
     * @var CategoryInterface
     */
    protected $category;

    /**
     * @var ObjectInterface[]
     */
    protected $objects = [];

    public function __construct(CategoryInterface $category)
    {
        $this->category = $category;
    }

    public function getElement()
    {
        return 'attribute/component/key_selector';
    }

    /**
     * @return mixed
     */
    public function getSelectAttributeUrl()
    {
        return $this->selectAttributeUrl;
    }

    /**
     * @param mixed $selectAttributeUrl
     */
    public function setSelectAttributeUrl($selectAttributeUrl): void
    {
        $this->selectAttributeUrl = $selectAttributeUrl;
    }

    /**
     * @return ObjectInterface[]
     */
    public function getObjects(): array
    {
        return $this->objects;
    }

    /**
     * @param ObjectInterface[] $objects
     */
    public function setObjects(array $objects): void
    {
        $this->objects = $objects;
    }

    public function view()
    {
        $serializer = new SetSerializer($this->category->getSetManager());
        $this->set('selectAttributeUrl', $this->selectAttributeUrl);
        $this->set('attributes', json_encode($serializer));
        $this->set('isBulkMode', json_encode(count($this->objects) > 1));

        $this->set('selectedAttributes', json_encode(
            count($this->objects) === 1
                ? new ObjectSerializer($this->category, head($this->objects))
                : new ObjectsSerializer($this->category, $this->objects)
        ));
    }
}
