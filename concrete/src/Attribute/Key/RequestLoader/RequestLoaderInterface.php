<?php
namespace Concrete\Core\Attribute\Key\RequestLoader;

use Concrete\Core\Entity\Attribute\Key\Key;
use Symfony\Component\HttpFoundation\Request;

interface RequestLoaderInterface
{
    public function load(Key $key, Request $request);
}
