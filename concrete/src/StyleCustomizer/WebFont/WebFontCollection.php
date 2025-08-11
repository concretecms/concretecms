<?php

namespace Concrete\Core\StyleCustomizer\WebFont;

use Doctrine\Common\Collections\ArrayCollection;

class WebFontCollection extends ArrayCollection implements \JsonSerializable
{

    /**
     * @param WebFont $font
     * @return bool|true
     */
    public function add($font)
    {
        return parent::add($font);
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

}
