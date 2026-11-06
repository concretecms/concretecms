<?php

namespace Concrete\Core\Api\Documentation;

use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Site\Service;

class RedirectUriFactory
{

    /**
     * @var Service
     */
    protected $siteService;

    /**
     * @param Service $siteService
     */
    public function __construct(Service $siteService)
    {
        $this->siteService = $siteService;
    }

    /**
     * @return string
     */
    public function createDocumentationRedirectUri(Client $client): string
    {
        return \URL::to('/ccm/system/api/documentation/redirect', $client->getIdentifier());
    }

}
