<?php
namespace Concrete\Core\Summary\Data;

use Concrete\Core\Summary\Data\Field\DataField;
use Concrete\Core\Summary\Data\Field\DataFieldInterface;
use Concrete\Core\Summary\Data\Field\FieldInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Responsible for joining fields in the system to actual data gleaned from an object. 
 */
class Collection implements \JsonSerializable, DenormalizableInterface
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
        return $this->collection->toArray();
    }

    /**
     * @param $field FieldInterface|string
     * @return bool
     */
    public function containsField($field)
    {
        $field = $field instanceof FieldInterface ? $field->getFieldIdentifier() : $field;
        $containsKey = $this->collection->containsKey($field);
        return $containsKey;
    }
    
    public function getField(string $field)
    {
        return $this->collection->get($field);
    }
    
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $json = [
            'fields' => []
        ];
        foreach($this->collection as $fieldIdentifier => $dataField) {
            $json['fields'][$fieldIdentifier] = $dataField;
        }
        return $json;
    }
    
    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        foreach($data['fields'] as $fieldIdentifier => $dataData) {
            $dataFieldData = $denormalizer->denormalize($dataData, $dataData['class'], 'json');
            $this->addField(new DataField($fieldIdentifier, $dataFieldData));
        }
    }

}
