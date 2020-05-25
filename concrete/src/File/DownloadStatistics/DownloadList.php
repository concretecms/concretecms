<?php

namespace Concrete\Core\File\DownloadStatistics;

class DownloadList
{
    private $hasMoreDownloads = false;

    private $list = [];

    public function hasMoreDownloads(): bool
    {
        return $this->hasMoreDownloads;
    }

    /**
     * @return $this
     */
    public function setHasMoreDownloads(bool $value): self
    {
        $this->hasMoreDownloads = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function add(Download $item): self
    {
        $this->list[] = $item;

        return $this;
    }

    /**
     * @return \Concrete\Core\File\DownloadStatistics\Download[]
     */
    public function getList(): array
    {
        return $this->list;
    }
}
