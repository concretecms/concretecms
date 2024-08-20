<?php

namespace Concrete\Core\Api\OpenApi;

class SpecSchema implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var string[]|null
     */
    protected $enum;

    public function __construct(?string $type = null, ?string $format = null, $enum = null)
    {
        $this->type = $type;
        $this->format = $format;
        $this->enum = $enum;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = ['type' => $this->getType()];
        if ($this->getFormat() !== null) {
            $data['format'] = $this->getFormat();
        }
        if ($this->enum !== null) {
            $data['enum'] = $this->enum;
        }
        return $data;
    }

}
