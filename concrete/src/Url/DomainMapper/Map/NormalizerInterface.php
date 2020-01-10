<?php
namespace Concrete\Core\Url\DomainMapper\Map;

/**
 * Interface NormalizerInterface
 * Normalize domains and paths
 *
 * @package PortlandLabs\DomainMapper\Map
 */
interface NormalizerInterface
{

    /**
     * @param string $domain
     * @return string
     */
    public function getDomain($domain);

    /**
     * @param string $path
     * @return string
     */
    public function normalizePath($path);

}
