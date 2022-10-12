<?php

namespace Concrete\Core\Api\Documentation;

use Concrete\Core\Site\Service;

class RedirectUriFactory
{

    const SWAGGER_OAUTH2_CALLBACK = DIR_REL . '/' . DIRNAME_CORE . '/api/swagger/oauth2-redirect.html';

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
    public function createDocumentationRedirectUri(): string
    {
        $site = $this->siteService->getSite();
        $baseUrl = rtrim($site->getSiteCanonicalUrl(), '/');
        return $baseUrl . self::SWAGGER_OAUTH2_CALLBACK;
    }

}
