<?php

declare(strict_types=1);

namespace Concrete\Core\Search\Column;

defined('C5_EXECUTE') or die('Access Denied.');

interface ColumnExportableInterface
{
    /**
     * Get the plain-text value to be used when exporting this column.
     */
    public function getColumnExportValue($mixed);
}
