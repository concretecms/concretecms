<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

interface ItemInterface
{
    /**
     * Get the display name of this item (representing the item class, not the found object).
     *
     * @return string
     */
    public function getDisplayName();

    /**
     * Get the reference found in the content.
     *
     * @return string|mixed
     *
     * @example a FileItem can return '123456789012:filename.ext' or 'filename.ext'
     */
    public function getReference();

    /**
     * Resolve the actual Concrete object.
     *
     * @return object|null returns null if the actual object can't be found
     *
     * @example a FileItem can return a File instance
     */
    public function getContentObject();

    /**
     * Get the value to be inserted in the content representing the object.
     *
     * @return string|mixed|null Return null if the actual object can't be found
     *
     * @example a FileItem can return '{CCM:FID_DL_123}'
     */
    public function getContentValue();

    /**
     * Get a value that uniquely identifies the found object.
     *
     * @return mixed|null Return null if the actual object can't be found
     *
     * @example a FileItem can return a file ID
     */
    public function getFieldValue();
}
