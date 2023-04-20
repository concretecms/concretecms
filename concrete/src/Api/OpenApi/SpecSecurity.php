<?php

namespace Concrete\Core\Api\OpenApi;

class SpecSecurity implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $schemeName;

    /**
     * @var string[]
     */
    protected $scopes = [];

    /**
     * SpecSecurity constructor.
     * @param string $schemeName
     * @param string[] $scopes
     */
    public function __construct(string $schemeName, array $scopes)
    {
        $this->schemeName = $schemeName;
        $this->scopes = $scopes;
    }

    /**
     * @return string
     */
    public function getSchemeName(): string
    {
        return $this->schemeName;
    }

    /**
     * @return string[]
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = [];
        $data[] = [
            $this->getSchemeName() => $this->getScopes()
        ];
        return $data;
    }


}
