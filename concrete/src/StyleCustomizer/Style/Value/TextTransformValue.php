<?php

namespace Concrete\Core\StyleCustomizer\Style\Value;

class TextTransformValue extends Value
{
    /**
     * @var string
     */
    protected $textTransform;

    /**
     * TextTransformValue constructor.
     * @param string $textTransform
     */
    public function __construct(string $textTransform)
    {
        $this->textTransform = $textTransform;
    }

    /**
     * @return string
     */
    public function getTextTransform(): string
    {
        return $this->textTransform;
    }

    /**
     * @param string $textTransform
     */
    public function setTextTransform(string $textTransform): void
    {
        $this->textTransform = $textTransform;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'textTransform' => $this->getTextTransform(),
        ];
    }




}
