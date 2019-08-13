<?php
namespace Concrete\Core\Import;

use Concrete\Core\Import\Item\ItemInterface;

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @since 8.0.0
 */
interface ImportableInterface
{

    /**
     * @return ItemInterface
     */
    public function getImporter();

}
