<?php

namespace Concrete\Core\Express\Association\Formatter;

use Concrete\Core\Entity\Express\Association;

abstract class AbstractFormatter implements FormatterInterface
{

    protected $association;

    public function __construct(Association $association)
    {
        $this->association = $association;
    }

    public function getDisplayName()
    {
        return sprintf(
            '%s > %s', $this->association->getTargetEntity()->getName(),
            $this->association->getSourceEntity()->getName()
        );
    }


}