<?php

namespace Concrete\Core\Application\UserInterface\Sitemap;

use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionJsonFormatter;
use JsonSerializable;

final class JsonFormatter implements JsonSerializable
{
    /**
     * @var \Concrete\Core\Application\UserInterface\Sitemap\ProviderInterface
     */
    protected $provider;

    /**
     * @param \Concrete\Core\Application\UserInterface\Sitemap\ProviderInterface $provider
     */
    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     *
     * @see JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize()
    {
        $data = [];

        if ($this->provider->includeMenuInResponse()) {
            $collection = $this->provider->getTreeCollection($this->provider->getRequestedSiteTree());
            $formatter = new TreeCollectionJsonFormatter($collection);
            $data['trees'] = $formatter;
            $data['children'] = $this->provider->getRequestedNodes();
            $siteTree = $this->provider->getRequestedSiteTree();
            $data['homeCID'] = $siteTree === null ? null : ((int) $siteTree->getSiteHomePageID() ?: null);
        } else {
            return $this->provider->getRequestedNodes();
        }

        return $data;
    }
}
