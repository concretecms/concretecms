<?php
namespace Concrete\Core\Attribute\Key\RequestLoader;

use Concrete\Core\Entity\Attribute\Key\Key;
use Symfony\Component\HttpFoundation\Request;

/**
 * @since 8.0.0
 */
interface RequestLoaderInterface
{
    public function load(Key $key, Request $request);
}
