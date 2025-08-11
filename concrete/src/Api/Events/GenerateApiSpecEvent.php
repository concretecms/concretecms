<?php

namespace Concrete\Core\Api\Events;

use OpenApi\Annotations\OpenApi;

class GenerateApiSpecEvent
{

    /**
     * @var OpenApi
     */
    protected $openApi;

    /**
     * @param OpenApi $openApi
     */
    public function __construct(OpenApi $openApi)
    {
        $this->openApi = $openApi;
    }

    /**
     * @return OpenApi
     */
    public function getOpenApi(): OpenApi
    {
        return $this->openApi;
    }

    /**
     * @param OpenApi $openApi
     */
    public function setOpenApi(OpenApi $openApi): void
    {
        $this->openApi = $openApi;
    }




}
