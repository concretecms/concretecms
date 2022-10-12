<?php

namespace Concrete\Core\Api\OpenApi;

class SpecSecurityScheme
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $scopes = [];

    /**
     * SecurityScheme constructor.
     * @param string $name
     * @param array $scopes
     */
    public function __construct(string $name, array $scopes)
    {
        $this->name = $name;
        $this->scopes = $scopes;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

}
