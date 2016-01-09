<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface;

abstract class AbstractItem implements ItemInterface
{

    protected $reference;

    public function getReference()
    {
        return $this->reference;
    }

    public function getContentValue()
    {
        return $this->getFieldValue();
    }

    public function __construct($reference)
    {
        $this->reference = $reference;
    }

}