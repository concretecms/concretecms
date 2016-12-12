<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector;

interface ResultInterface
{
    public function getMatchedItems();
    public function getReplacedContent();
    public function getReplacedValue();
}
