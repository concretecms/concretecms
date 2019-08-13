<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

/**
 * @since 5.7.5.3
 */
interface ItemInterface
{
    public function getDisplayName();
    public function getReference();
    public function getContentObject();
    public function getContentValue();
    public function getFieldValue();
}
