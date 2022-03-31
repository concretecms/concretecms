<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Doctrine\Common\Collections\ArrayCollection;

class CustomizerVariableCollection extends ArrayCollection implements \JsonSerializable
{

    /**
     * @param CustomizerVariable $variable
     * @return bool|true
     */
    public function add($variable)
    {
        return parent::add($variable);
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

}
