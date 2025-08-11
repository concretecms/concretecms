<?php

namespace Concrete\Core\StyleCustomizer\Style\Value;

class TextDecorationValue extends Value
{
    /**
     * @var string
     */
    protected $textDecoration;

    /**
     * TextDecorationValue constructor.
     * @param string $textDecoration
     */
    public function __construct(string $textDecoration)
    {
        $this->textDecoration = $textDecoration;
    }

    /**
     * @return string
     */
    public function getTextDecoration(): string
    {
        return $this->textDecoration;
    }

    /**
     * @param string $textDecoration
     */
    public function setTextDecoration(string $textDecoration): void
    {
        $this->textDecoration = $textDecoration;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'textDecoration' => $this->gettextDecoration(),
        ];
    }




}
