<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

class PictureItem extends FileItem
{
    /**
     * The attributes of the <concrete-picture> element other than the one naming the file.
     *
     * @var string
     */
    protected $additionalAttributes;

    /**
     * @param string $filename
     * @param string|null $prefix
     * @param string $additionalAttributes the attributes of the element other than the one naming the file
     */
    public function __construct($filename, $prefix = null, string $additionalAttributes = '')
    {
        parent::__construct($filename, $prefix);
        $this->additionalAttributes = $additionalAttributes;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getDisplayName()
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\FileItem::getDisplayName()
     */
    public function getDisplayName()
    {
        return t('Picture');
    }

    /**
     * Get the attributes of the <concrete-picture> element other than the one naming the file.
     */
    public function getAdditionalAttributes(): string
    {
        return $this->additionalAttributes;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getContentValue()
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\FileItem::getContentValue()
     *
     * @return string|null
     */
    public function getContentValue()
    {
        $file = $this->getContentObject();
        if ($file === null) {
            return null;
        }
        $attributes = $this->getAdditionalAttributes();

        return "<concrete-picture fID=\"{$file->getFileID()}\"" . ($attributes === '' ? '' : " {$attributes}") . ' />';
    }
}
