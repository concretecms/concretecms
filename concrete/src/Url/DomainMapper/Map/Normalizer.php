<?php
namespace Concrete\Core\Url\DomainMapper\Map;

use Concrete\Core\Url\Components\Path;
use League\Url\Url;

/**
 * Class Normalizer
 * Normalize paths and domains in a standard way
 */
class Normalizer implements NormalizerInterface
{

    /**
     * @param string $domain
     * @return string
     */
    public function getDomain($url)
    {
        $url = Url::createFromUrl($url);
        return (string) $url->getHost();
    }

    /**
     * Trim trailing slash, ensure there's a leading slash
     *
     * @param string $path
     * @return string
     */
    public function normalizePath($path)
    {
        $path = new Path('/' . trim($path, '/ '), true);
        return "/" . (string)$path->withoutDispatcher();
    }

}
