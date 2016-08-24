<?php
namespace Concrete\Core\Site\Config;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Site\Site;

/**
 * A repository stand-in that allows for accessing a site's config
 * @package Concrete\Core\Site\Config
 */
class Liaison extends \Concrete\Core\Config\Repository\Liaison
{

    protected $site;

    public function __construct(Repository $repository, Site $site)
    {
        $this->site = $site;
        parent::__construct($repository, null);
    }

    /**
     * Prepend the "site" config group and the current site handle
     * @param $key
     * @return string
     */
    protected function transformKey($key)
    {
        $key = sprintf('site.sites.%s.%s', $this->site->getSiteHandle(), $key);
        return parent::transformKey($key);
    }

}
