<?php

namespace Concrete\Core\StyleCustomizer\Parser\Normalizer;

use Doctrine\Common\Collections\ArrayCollection;

class NormalizedVariableCollection extends ArrayCollection
{

    /**
     * @param Variable $element
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


}
