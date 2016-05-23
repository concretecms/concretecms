<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

interface ItemInterface
{
    public function getDisplayName();
    public function getReference();
    public function getContentObject();
    public function getContentValue();
    public function getFieldValue();
}
