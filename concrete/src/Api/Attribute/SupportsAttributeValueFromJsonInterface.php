<?php

namespace Concrete\Core\Api\Attribute;

interface SupportsAttributeValueFromJsonInterface
{

    /**
     * Could be a string, could be an array representation of a more complex request body object
     * @param mixed $json
     * @return mixed
     */
    public function createAttributeValueFromNormalizedJson($json);


}