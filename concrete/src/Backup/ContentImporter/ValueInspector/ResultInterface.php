<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector;

interface ResultInterface
{
    /**
     * Get the items matched by inspection routines.
     *
     * @return \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface[]
     */
    public function getMatchedItems();

    /**
     * Get the content with the replaced values.
     *
     * @return string|mixed
     */
    public function getReplacedContent();

    /**
     * Get the value replacing the content (or the original content if no replacements has been found).
     *
     * @return string|mixed
     */
    public function getReplacedValue();
}
