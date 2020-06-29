<?php

namespace Concrete\Core\Board\Designer\Command;

class CreateCustomElementCommand
{

    /**
     * @var string
     */
    protected $elementName;

    /**
     * @var string
     */
    protected $creationMethod;

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

    /**
     * @return string
     */
    public function getCreationMethod(): string
    {
        return $this->creationMethod;
    }

    /**
     * @param string $creationMethod
     */
    public function setCreationMethod(string $creationMethod): void
    {
        $this->creationMethod = $creationMethod;
    }

}
