<?php

namespace Concrete\Core\Board\Designer\Command;

use Concrete\Core\Foundation\Command\Command;

abstract class AbstractCustomElementCommand extends Command
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
