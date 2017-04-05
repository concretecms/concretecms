<?php
namespace Concrete\Core\Application\UserInterface\Sitemap;

use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionInterface;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionJsonFormatter;

final class JsonFormatter implements \JsonSerializable
{

    protected $provider;

    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function jsonSerialize()
    {
        $data = array();

        if ($this->provider->includeMenuInResponse()) {
            $collection = $this->provider->getTreeCollection($this->provider->getRequestedSiteTree());
            $formatter = new TreeCollectionJsonFormatter($collection);
            $data['trees'] = $formatter;
            $data['children'] = $this->provider->getRequestedNodes();
        } else {
            return $this->provider->getRequestedNodes();
        }


        return $data;
    }

}
