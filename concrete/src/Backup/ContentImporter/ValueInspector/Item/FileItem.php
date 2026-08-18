<?php

declare(strict_types=1);

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

use Concrete\Core\Entity\File\File;
use Doctrine\ORM\EntityManagerInterface;

class FileItem implements ItemInterface
{
    /**
     * The regular expression (without delimiters) matching a file identifier, that is a file ID or a file UUID.
     * It's meant to be used with the case-insensitive and the dollar-endonly modifiers.
     *
     * @var string
     */
    public const IDENTIFIER_REGEX = '[1-9][0-9]*|[0-9a-f]{8}(?:-[0-9a-f]{4}){3}-[0-9a-f]{12}';

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
     * The found file ID (int) or file UUID (string) - an empty string if not found.
     *
     * @var int|string
     */
    protected $fileID;

    /**
     * @param string $filename the file name (without the potential prefix)
     * @param string|null $prefix the found prefix (if any)
     * @param int|string $fileID the found file ID (int) or file UUID (string) - an empty string if not found
     */
    public function __construct($filename, $prefix = null, $fileID = '')
    {
        $this->filename = $filename;
        $this->prefix = $prefix;
        $this->fileID = $fileID;
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
        $filename = $this->getFilename();
        $prefix = (string) $this->getPrefix();
        if ($filename !== '' || $prefix !== '') {
            return $prefix === '' ? $filename : "{$prefix}:{$filename}";
        }
        $id = $this->getFileID();
        if ($id !== '') {
            return "id={$id}";
        }

        return '';
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
     * Get the found file ID (int) or file UUID (string) - an empty string if not found.
     * It's meaningful only if the file name and the prefix are empty.
     *
     * @return int|string
     */
    public function getFileID()
    {
        return $this->fileID;
    }

    /**
     * Parse a file identifier found in the content: it can be a file ID or a file UUID.
     *
     * @param string|int|mixed $value
     *
     * @return int|string the file ID (int) or the file UUID (string) - an empty string if the value is neither of them
     */
    public static function parseFileIdentifier($value)
    {
        $value = is_string($value) || is_int($value) ? trim((string) $value) : '';
        if (!preg_match('/^(?:' . self::IDENTIFIER_REGEX . ')$/Di', $value)) {
            return '';
        }

        return preg_match('/^[0-9]+$/D', $value) ? (int) $value : $value;
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
        $filename = $this->getFilename();
        if ($filename === '') {
            $fileID = $this->getFileID();
            if ($fileID === '') {
                return null;
            }
            if (!is_int($fileID)) {
                return $em->getRepository(File::class)->findOneBy(['fUUID' => $fileID]);
            }

            return $em->find(File::class, $fileID);
        }
        $db = $em->getConnection();
        $prefix = (string) $this->getPrefix();
        if ($prefix === '') {
            $fID = $db->fetchOne(
                'SELECT fID FROM FileVersions WHERE fvFilename = ? LIMIT 1',
                [$filename]
            );
        } else {
            $fID = $db->fetchOne(
                'SELECT fID FROM FileVersions WHERE fvPrefix = ? AND fvFilename = ? LIMIT 1',
                [$prefix, $filename]
            );
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
