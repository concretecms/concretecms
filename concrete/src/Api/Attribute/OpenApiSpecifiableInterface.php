<?php

namespace Concrete\Core\Api\Attribute;

use Concrete\Core\Api\OpenApi\SpecProperty;
use Concrete\Core\Entity\Attribute\Key\Key;

interface OpenApiSpecifiableInterface
{

    public function getOpenApiSpecProperty(Key $key): SpecProperty;


}