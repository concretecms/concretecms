<?php

namespace Concrete\Core\StyleCustomizer\Normalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class NormalizedVariableCollection extends ArrayCollection implements \JsonSerializable, DenormalizableInterface
{

    /**
     * @param VariableInterface $element
     * @return bool|true
     */
    public function add($element)
    {
        return parent::add($element);
    }

    public function getVariable(string $name): ?VariableInterface
    {
        foreach ($this->toArray() as $variable) {
            if ($variable->getName() == $name) {
                return $variable;
            }
        }
        return null;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        foreach ($data as $value) {
            if (isset($value['type']) && $value['type'] == 'number') {
                $this->add(new NumberVariable($value['name'], $value['number'], $value['unit']));
            } else {
                $this->add(new Variable($value['name'], $value['value']));
            }
        }
    }

}
