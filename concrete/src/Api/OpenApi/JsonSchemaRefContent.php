<?php

namespace Concrete\Core\Api\OpenApi;

class JsonSchemaRefContent implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $ref;

    /**
     * JsonSchemaRefContent constructor.
     * @param string $ref
     */
    public function __construct(string $ref)
    {
        $this->ref = $ref;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'application/json' => [
                'schema' => [
                    '$ref' => '#' . $this->ref
                ]
            ]
        ];
    }


}
