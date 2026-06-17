<?php

namespace Concrete\Core\Api\OpenApi;

class SpecRequestBody implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $modelName;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * @param string $ref
     */
    public function __construct(?string $modelName = null, ?string $description = null)
    {
        $this->modelName = $modelName;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getModelName(): ?string
    {
        return $this->modelName;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = [
            'description' => $this->description,
            'required' => true,
            'content' => [
                'application/json' => [
                    'schema' => [
                        '$ref' => '#/components/schemas/' . $this->modelName,
                    ],
                ],
            ],
        ];
        return $data;
    }

}
