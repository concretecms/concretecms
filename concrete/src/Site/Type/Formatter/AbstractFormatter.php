<?php

namespace Concrete\Core\Site\Type\Formatter;

use Concrete\Core\Entity\Site\Type;

abstract class AbstractFormatter implements FormatterInterface
{
    /**
     * @var \Concrete\Core\Entity\Site\Type
     */
    protected $type;

    public function __construct(Type $type)
    {
        $this->type = $type;
    }
}
