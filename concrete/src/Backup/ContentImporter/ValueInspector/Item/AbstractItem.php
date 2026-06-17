<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

abstract class AbstractItem implements ItemInterface
{
    /**
     * @var string|mixed
     */
    protected $reference;

    /**
     * @param string|mixed $reference the reference found in the content
     */
    public function __construct($reference)
    {
        $this->reference = $reference;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getReference()
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getContentValue()
     */
    public function getContentValue()
    {
        return $this->getFieldValue();
    }
}
