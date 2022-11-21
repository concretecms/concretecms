<?php

namespace Concrete\Core\Api\OpenApi;

class FileContent implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $fileType;

    /**
     * @param string $fileType
     */
    public function __construct(string $fileType)
    {
        $this->fileType = $fileType;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            $this->fileType => [
                'schema' => [
                    'type' => 'string',
                    'format' => 'binary',
                ]
            ]
        ];
    }


}
