<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

class PictureItem extends FileItem
{
    /**
     * @var string
     */
    protected $additionalAttributes;

    /**
     * @param string $filename the file name (without the potential prefix)
     * @param string|null $prefix the found prefix (if any)
     * @param int|string $fileID the found file ID (int) or file UUID (string) - an empty string if not found
     * @param string $additionalAttributes the other attributes of the <concrete-picture> element
     */
    public function __construct($filename, $prefix = null, $fileID = '', string $additionalAttributes = '')
    {
        parent::__construct($filename, $prefix, $fileID);
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
        $result = "<concrete-picture fID=\"{$file->getFileID()}\"";
        if (($additionalAttributes = $this->getAdditionalAttributes()) !== '') {
            $result .= " {$additionalAttributes}";
        }
        $result .= ' />';

        return $result;
    }
}
