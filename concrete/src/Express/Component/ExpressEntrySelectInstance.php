<?php

namespace Concrete\Core\Express\Component;

use Concrete\Core\Entity\Express\Entry;

class ExpressEntrySelectInstance
{

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $entityHandle;

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

    /**
     * @return string
     */
    public function getEntityHandle(): string
    {
        return $this->entityHandle;
    }

    /**
     * @param string $entityHandle
     */
    public function setEntityHandle(string $entityHandle): void
    {
        $this->entityHandle = $entityHandle;
    }


    public function createResultFromEntry(Entry $entry): array
    {
        $data = [
            'id' => $entry->getID(),
            'primary_label' => $entry->getLabel()
        ];

        return $data;
    }


}
