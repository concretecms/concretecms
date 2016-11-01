<?php

namespace Concrete\Core\File\Tracker;

use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Statistics\UsageTracker\TrackableInterface;

interface FileTrackableInterface extends TrackableInterface
{

    /**
     * @return Collection The collection these files are attached to
     */
    public function getUsedCollection();

    /**
     * @return array An array of file IDs or file objects
     */
    public function getUsedFiles();

}
