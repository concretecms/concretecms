<?php

namespace Concrete\Core\Api\Attribute;

use League\Fractal\TransformerAbstract;

interface SimpleApiAttributeValueInterface
{

    /**
     * @return mixed
     */
    public function getApiAttributeValue();


}