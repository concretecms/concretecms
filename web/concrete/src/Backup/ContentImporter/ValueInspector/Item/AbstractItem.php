<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

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
