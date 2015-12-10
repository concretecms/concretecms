<?php

namespace Concrete\Core\Attribute\Key\RequestLoader;

use Concrete\Core\Entity\AttributeKey\AttributeKey;
use Symfony\Component\HttpFoundation\Request;

interface RequestLoaderInterface
{

    public function load(AttributeKey $key, Request $request);

}
