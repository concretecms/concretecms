<?php

namespace Concrete\Core\StyleCustomizer\Normalizer\Legacy;

use Concrete\Core\StyleCustomizer\Normalizer\ImageVariable as BaseImageVariable;

class ImageVariable extends BaseImageVariable
{

    public function getValue()
    {
        return "'" . $this->getComputedUrl() . "'";
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();
        $data['type'] = 'legacy-image';
    }

}
