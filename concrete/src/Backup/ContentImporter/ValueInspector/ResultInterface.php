<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector;

/**
 * @since 5.7.5.4
 */
interface ResultInterface
{
    public function getMatchedItems();
    public function getReplacedContent();
    public function getReplacedValue();
}
