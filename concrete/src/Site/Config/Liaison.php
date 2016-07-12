<?php
namespace Concrete\Core\Site\Config;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Site\Site;

class Liaison extends \Concrete\Core\Config\Repository\Liaison
{

    protected $site;

    public function __construct(Repository $repository, Site $site)
    {
        $this->site = $site;
        parent::__construct($repository, null);
    }

    protected function transformKey($key)
    {
        $key = sprintf('%s.%s.%s.%s', 'concrete', 'sites', $this->site->getSiteHandle(), $key);
        return parent::transformKey($key);
    }

}
