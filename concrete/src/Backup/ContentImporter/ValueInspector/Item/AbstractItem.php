<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

/**
 * @since 5.7.5.3
 */
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
