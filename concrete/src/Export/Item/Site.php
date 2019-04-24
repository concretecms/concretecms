<?php
namespace Concrete\Core\Export\Item;

use Concrete\Core\Attribute\Category\SiteCategory;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Site\Service;

class Site implements ItemInterface
{

    protected $service;
    protected $attributeCategory;

    public function __construct(Service $service, SiteCategory $attributeCategory)
    {
        $this->attributeCategory = $attributeCategory;
        $this->service = $service;
    }

    /**
     * @param $site \Concrete\Core\Entity\Site\Site
     * @param \SimpleXMLElement $xml
     * @return mixed
     */
    public function export($site, \SimpleXMLElement $xml)
    {

        $xml = $xml->addChild('site');
        $xml->addAttribute('name', $site->getSiteName());
        $xml->addAttribute('handle', $site->getSiteHandle());
        $xml->addAttribute('type', $site->getType()->getSiteTypeHandle());
        $xml->addAttribute('canonical-url', (string) $site->getSiteCanonicalURL());

        $domains = $this->service->getSiteDomains($site);
        if (count($domains)) {
            $node = $xml->addChild('domains');
            foreach($domains as $domain) {
                $node->addChild('domain', $domain->getDomain());
            }
        }

        $attributes = $xml->addChild('attributes');
        foreach ($this->attributeCategory->getAttributeValues($site) as $av) {
            $ak = $av->getAttributeKey();
            $cnt = $ak->getController();
            $cnt->setAttributeValue($av);
            $akx = $attributes->addChild('attributekey');
            $akx->addAttribute('handle', $ak->getAttributeKeyHandle());
            $cnt->exportValue($akx);
        }

        return $xml;
    }

}
