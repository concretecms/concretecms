<?php

namespace Concrete\Core\File\DownloadStatistics;

use DateTimeImmutable;

class Download
{
    /**
     * @var int
     */
    private $downloadID;

    /**
     * @var int
     */
    private $fileID;

    /**
     * @var int
     */
    private $fileVersionID;

    /**
     * @var int|null
     */
    private $userID;

    /**
     * @var int|null
     */
    private $pageID;

    /**
     * @var \DateTimeImmutable
     */
    private $timestamp;

    public static function create(int $downloadID, int $fileID, int $fileVersionID, ?int $userID, ?int $pageID, DateTimeImmutable $timestamp)
    {
        $result = new static();
        $result->downloadID = $downloadID;
        $result->fileID = $fileID;
        $result->fileVersionID = $fileVersionID;
        $result->userID = $userID;
        $result->pageID = $pageID;
        $result->timestamp = $timestamp;

        return $result;
    }

    public function getDownloadID(): int
    {
        return $this->downloadID;
    }

    public function getFileID(): int
    {
        return $this->fileID;
    }

    public function getFileVersionID(): int
    {
        return $this->fileVersionID;
    }

    public function getUserID(): ?int
    {
        return $this->userID;
    }

    public function getPageID(): ?int
    {
        return $this->pageID;
    }

    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }
}
