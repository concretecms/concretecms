<?php
namespace Concrete\Core\Export;

use Concrete\Core\Export\Item\ItemInterface;

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @since 8.0.0
 */
interface ExportableInterface
{

    /**
     * @return ItemInterface
     */
    public function getExporter();

}
