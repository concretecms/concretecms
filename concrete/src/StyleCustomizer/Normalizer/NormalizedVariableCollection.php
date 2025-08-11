<?php

namespace Concrete\Core\StyleCustomizer\Normalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Concrete\Core\StyleCustomizer\Normalizer\Legacy\ImageVariable as LegacyImageVariable;
/**
 * A way to normalize SCSS/LESS variables that can be injected into different compilers, stored in the database
 * etc...
 */
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

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        foreach ($data as $value) {
            if (isset($value['type']) && $value['type'] == 'number') {
                $this->add(new NumberVariable($value['name'], $value['number'], $value['unit']));
            } else if (isset($value['type']) && $value['type'] == 'image') {
                $this->add(new ImageVariable($value['name'], $value['url'], $value['fID']));
            } else if (isset($value['type']) && $value['type'] == 'legacy-image') {
                $this->add(new LegacyImageVariable($value['name'], $value['url'], $value['fID']));
            } else {
                $this->add(new Variable($value['name'], $value['value']));
            }
        }
    }

}
