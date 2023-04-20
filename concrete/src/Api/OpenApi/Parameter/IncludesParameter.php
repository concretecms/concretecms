<?php

namespace Concrete\Core\Api\OpenApi\Parameter;

class IncludesParameter implements ParameterInterface
{

    /**
     * @var string[]
     */
    protected $includes;

    public function __construct(array $includes)
    {
        $this->includes = $includes;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'name' => 'includes',
            'in' => 'query',
            'explode' => false,
            'schema' => [
                'type' => 'array',
                'items' => [
                    'type' => 'string',
                    'enum' => $this->includes,
                ],
            ],
        ];
    }

}
