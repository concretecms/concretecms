<?php

namespace Concrete\Core\Express\Entry\Formatter;

use Concrete\Core\Entity\Express\Entry;

interface EntryFormatterInterface
{
    /**
     * Format a mask given an entry
     *
     * Mask format will typically be something like `Example %attribute_key% %association_handle`
     *
     * @param string $mask
     * @param \Concrete\Core\Entity\Express\Entry $entry
     * @return string|null
     */
    public function format($mask, Entry $entry);

    /**
     * Given no mask, we inspect the attributes on an entry to retrieve a
     * display text label for it.
     * @param Entry $entry
     * @return mixed
     */
    public function getLabel(Entry $entry);
}
