<?php
namespace Concrete\Controller\Element\Attribute\Component;

use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Key\Component\KeySelector\ObjectSerializer;
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
     * @var ObjectInterface
     */
    protected $object;

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
     * @return ObjectInterface
     */
    public function getObject(): ObjectInterface
    {
        return $this->object;
    }

    /**
     * @param ObjectInterface $object
     */
    public function setObject(ObjectInterface $object): void
    {
        $this->object = $object;
    }

    public function __construct(CategoryInterface $category)
    {
        $this->category = $category;
    }


    public function view()
    {
        $serializer = new SetSerializer($this->category->getSetManager());
        $this->set('selectAttributeUrl', $this->selectAttributeUrl);
        $this->set('attributes', json_encode($serializer));
        if ($this->object) {
            $this->set('selectedAttributes', json_encode(
                new ObjectSerializer($this->category, $this->object)
            ));
        }
    }
}
