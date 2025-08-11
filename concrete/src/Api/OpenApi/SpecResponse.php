<?php

namespace Concrete\Core\Api\OpenApi;

class SpecResponse implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var mixed
     */
    protected $content;

    /**
     * SpecResponse constructor.
     * @param string $code
     * @param string $description
     */
    public function __construct(string $code, string $description, $content = null)
    {
        $this->code = $code;
        $this->description = $description;
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = [];
        $data['description'] = $this->getDescription();
        if ($this->content) {
            $data['content'] = $this->getContent();
        }
        return $data;
    }

}
