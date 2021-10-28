<?php

namespace Concrete\Core\Entity\File;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="DownloadStatistics",
 *     indexes={
 *         @ORM\Index(name="fID", columns={"fID", "timestamp"}),
 *         @ORM\Index(name="fvID", columns={"fID", "fvID"}),
 *         @ORM\Index(name="uID", columns={"uID"}),
 *         @ORM\Index(name="rcID", columns={"rcID"}),
 *         @ORM\Index(name="timestamp", columns={"timestamp"})
 *     },
 *     options={
 *         "comment": "List of downloaded files"
 *     }
 * )
 */
class DownloadStatistics
{
    /**
     * The downloadStatistics record ID.
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="dsID", nullable=false, options={"unsigned": true, "comment": "DownloadStatistics record ID"})
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int|null NULL if not yet saved
     */
    protected $id;

    /**
     * The downloaded file.
     *
     * @ORM\ManyToOne(targetEntity="File")
     * @ORM\JoinColumn(name="fID", referencedColumnName="fID", nullable=false, onDelete="CASCADE")
     *
     * @var \Concrete\Core\Entity\File\File
     */
    protected $file;

    /**
     * The version of the downloaded file.
     *
     * @ORM\Column(type="integer", name="fvID", nullable=false, options={"unsigned": false, "comment": "Version of the downloaded file"})
     *
     * @var int
     */
    protected $fileVersion;

    /**
     * The ID of the user that downloaded the file.
     *
     * @ORM\Column(type="integer", name="uID", nullable=true, options={"unsigned": true, "comment": "ID of the user that downloaded the file"})
     *
     * @var int|null
     */
    protected $downloaderID;

    /**
     * The ID of the page where the download originated.
     *
     * @ORM\Column(type="integer", name="rcID", nullable=true, options={"unsigned": true, "comment": "ID of the page where the download originated"})
     *
     * @var int|null
     */
    protected $relatedPageID;

    /**
     * The date/time when the file has been downloaded.
     *
     * @ORM\Column(type="datetime_immutable", name="timestamp", nullable=false, columnDefinition="TIMESTAMP DEFAULT current_timestamp", options={"comment": "Date/time when the file has been downloaded"})
     *
     * @var \DateTimeImmutable
     */
    protected $downloadDateTime;

    protected function __construct()
    {
    }

    /**
     * @return static
     */
    public static function create(File $file, int $fileVersion, ?int $downloaderID, ?int $relatedPageID, ?DateTimeImmutable $downloadDateTime = null): self
    {
        $result = new static();
        $result->file = $file;
        $result->fileVersion = $fileVersion;
        $result->downloaderID = $downloaderID ?: null;
        $result->relatedPageID = $relatedPageID ?: null;
        $result->downloadDateTime = $downloadDateTime ?: new DateTimeImmutable('now');

        return $result;
    }

    /**
     * Get tthe downloadStatistics record ID.
     *
     * @return int|null NULL if not yet saved
     */
    public function getID(): ?int
    {
        return $this->id;
    }

    /**
     * Get the downloaded file.
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * Get the version of the downloaded file.
     */
    public function getFileVersion(): int
    {
        return $this->fileVersion;
    }

    /**
     * Get the ID of the user that downloaded the file.
     */
    public function getDownloaderID(): ?int
    {
        return $this->downloaderID;
    }

    /**
     * Get the ID of the user that downloaded the file.
     */
    public function getRelatedPageID(): ?int
    {
        return $this->relatedPageID;
    }

    /**
     * Get the ID of the user that downloaded the file.
     */
    public function getDownloadDateTime(): DateTimeImmutable
    {
        return $this->downloadDateTime;
    }
}
