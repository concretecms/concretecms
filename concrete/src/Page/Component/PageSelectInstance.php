<?php

namespace Concrete\Core\Page\Component;

use Concrete\Core\Page\Page;

class PageSelectInstance
{

    protected $accessToken;

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param mixed $accessToken
     */
    public function setAccessToken($accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function createResultFromPage(Page $c): array
    {
        $data = [
            'id' => $c->getCollectionID(),
            'primary_label' => $c->getCollectionName()
        ];

        return $data;
    }


}
