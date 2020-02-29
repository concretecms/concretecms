<?php
namespace Concrete\Core\Site\Resolver;

use Concrete\Core\Site\Service;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Url\DomainMapper\Map\NormalizerInterface;
use Concrete\Core\Site\Selector;
use Concrete\Core\Http\Request;

class MultisiteDriver implements DriverInterface
{

    protected $selector;
    protected $normalizer;

    public function __construct(Selector $selector, NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
        $this->selector = $selector;
    }

    public function getActiveSiteForEditing(Service $service, Request $request)
    {
        $id = $this->selector->getSiteIDFromSession();
        if ($id) {
            $site = $service->getByID($id);
        }

        if (isset($site) && is_object($site)) {
            return $site;
        } else {
            return $service->getSite();
        }
    }

    public function getSite(Service $service, Request $request)
    {
        // First, attempt to ascertain which site we should be at based on the REQUEST
        // Domain mapper style.
        if (php_sapi_name() == 'cli') {
            return $service->getDefault();
        }

        $domain = $this->normalizer->getDomain($request->getUri());
        $found = false;

        $config = Facade::getFacadeApplication()->make('config');
        $siteConfig = $config->get('site');
        foreach((array) $siteConfig['sites'] as $handle => $siteEntry) {
            if (!empty($siteEntry['seo']['canonical_url'])) {
                $canonicalUrl = $siteEntry['seo']['canonical_url'];
                if ($this->normalizer->getDomain($canonicalUrl) == $domain) {
                    $found = $service->getByHandle($handle);
                }
            }
        }

        if (!$found) {
            // Check by domain.
            $found = $service->getSiteByDomain($domain);
        }

        if ($found && !$found->isDefault()) {
            return $found;
        }

        return $service->getDefault();
    }


}