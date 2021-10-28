<?php

namespace Concrete\Core\Design\Tag;

class Tag implements TagInterface
{

    /**
     * @var string
     */
    protected $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString()
    {
        return (string)$this->value;
    }


}