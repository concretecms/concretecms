<?php

namespace Concrete\Core\Logging\Entry;

use Concrete\Core\User\User;

abstract class ApplierEntry implements EntryInterface
{

    final public function getMessage()
    {
        if ($this->applier && $this->applier->isRegistered()) {
            return $this->getEntryMessageWithApplier();
        } else {
            return $this->getEntryMessage();
        }
    }

    /**
     * The user performing the operation
     *
     * @var User | null
     */
    protected $applier;

    public function __construct(User $applier = null)
    {
        $this->applier = $applier;
    }

    abstract public function getEntryMessage();
    abstract public function getEntryMessageWithApplier();
    abstract public function getEntryOperation();
    abstract public function getEntryContext();

    final public function getContext()
    {
        $context = $this->getEntryContext();
        $context['operation'] = $this->getEntryOperation();
        return $context;
    }
}
