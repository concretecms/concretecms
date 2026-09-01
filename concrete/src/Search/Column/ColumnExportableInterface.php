<?php

declare(strict_types=1);

namespace Concrete\Core\Search\Column;

defined('C5_EXECUTE') or die('Access Denied.');

interface ColumnExportableInterface
{
    /**
     * Get the plain-text value to be used when exporting this column.
     *
     * Implement this interface whenever getColumnValue() returns something that is
     * not already plain text (markup, a \DateTimeInterface, etc.): the exporter does
     * not attempt to strip or reformat display values on a column's behalf. The
     * returned value should be a scalar, a \DateTimeInterface, or an iterable of
     * such values (multiple values will be joined with "; " by the exporter). Any
     * hierarchy/grouping formatting the exported value should retain is the
     * responsibility of this method.
     *
     * @return mixed
     */
    public function getColumnExportValue($mixed);
}