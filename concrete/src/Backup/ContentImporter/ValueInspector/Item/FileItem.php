<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

use Concrete\Core\Entity\File\File;
use Doctrine\ORM\EntityManagerInterface;

class FileItem implements ItemInterface
{
    /**
     * The file name (without the potential prefix).
     *
     * @var string
     */
    protected $filename;

    /**
     * The found prefix (if any).
     *
     * @var string|null
     */
    protected $prefix;

    /**
     * @param string $filename the file name (without the potential prefix)
     * @param string|null $prefix the found prefix (if any)
     */
    public function __construct($filename, $prefix = null)
    {
        $this->filename = $filename;
        $this->prefix = $prefix;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getDisplayName()
     */
    public function getDisplayName()
    {
        return t('File');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getReference()
     */
    public function getReference()
    {
        $prefix = $this->getPrefix();

        return (string) $prefix === '' ? $this->getFilename() : "{$prefix}:{$this->getFilename()}";
    }

    /**
     * Get the file name (without the potential prefix).
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Get the found prefix (if any).
     *
     * @return string|null
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getContentObject()
     *
     * @return \Concrete\Core\Entity\File\File|null
     */
    public function getContentObject()
    {
        $em = app(EntityManagerInterface::class);
        $db = $em->getConnection();
        $prefix = (string) $this->getPrefix();
        if ($prefix === '') {
            $fID = $db->fetchOne(
                'SELECT fID FROM FileVersions WHERE fvFilename = ? LIMIT 1',
                [$this->getFilename()]
            );
        } else {
            $fID = $db->fetchOne(
                'SELECT fID FROM FileVersions WHERE fvPrefix = ? AND fvFilename = ? LIMIT 1',
                [$prefix, $this->getFilename()]
            );
        }
        if (!$fID) {
            return null;
        }

        return $fID ? $em->find(File::class, $fID) : null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getContentValue()
     *
     * @return string|null
     */
    public function getContentValue()
    {
        $file = $this->getContentObject();
        if ($file === null) {
            return null;
        }
        $uuid = $file->getFileUUID();

        return sprintf('{CCM:FID_DL_%s}', $uuid ?: $file->getFileID());
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getFieldValue()
     *
     * @return int|null
     */
    public function getFieldValue()
    {
        $file = $this->getContentObject();

        return $file ? $file->getFileID() : null;
    }
}
