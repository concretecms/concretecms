<?php

namespace Concrete\Core\Board\Designer\Command;

abstract class AbstractCustomElementCommand
{

    /**
     * @var string
     */
    protected $elementName;

    /**
     * @return string
     */
    public function getElementName(): string
    {
        return $this->elementName;
    }

    /**
     * @param string $elementName
     */
    public function setElementName(string $elementName): void
    {
        $this->elementName = $elementName;
    }


}
