<?php

namespace Concrete\Core\Install;

/**
 * The result of a precondition check.
 */
abstract class AbstractListablePrecondition extends AbstractPrecondition implements ListablePreconditionInterface
{

    public function getComponent(): string
    {
        return 'precondition';
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();
        $data['component'] = $this->getComponent();
        return $data;
    }
}
