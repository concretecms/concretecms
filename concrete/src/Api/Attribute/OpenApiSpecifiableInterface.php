<?php

namespace Concrete\Core\Api\Attribute;

use Concrete\Core\Api\OpenApi\SpecProperty;

interface OpenApiSpecifiableInterface
{

    public function getOpenApiSpecProperty(): SpecProperty;


}