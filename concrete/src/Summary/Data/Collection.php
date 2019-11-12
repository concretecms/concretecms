<?php
namespace Concrete\Core\Summary\Data;

use Concrete\Core\Summary\Data\Field\DataFieldInterface;
use Concrete\Core\Summary\Data\Field\FieldInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Responsible for joining fields in the system to actual data gleaned from an object. 
 */
class Collection
{
    /**
     * @var ArrayCollection 
     */
    private $collection;
    
    public function __construct()
    {
        $this->collection = new ArrayCollection();
    }

    public function addField(DataFieldInterface $field)
    {
        $this->collection->set(
            $field->getFieldIdentifier(), 
            $field->getData()
        );
    }
    
    public function getFields()
    {
        return $this->collection->getKeys();
    }

    /**
     * @param $field FieldInterface|string
     * @return bool
     */
    public function containsField($field)
    {
        $field = $field instanceof FieldInterface ? $field->getFieldIdentifier() : $field;
        return $this->collection->containsKey($field);
    }
    
    public function getField(string $field)
    {
        return $this->collection->get($field);
    }
    

}
