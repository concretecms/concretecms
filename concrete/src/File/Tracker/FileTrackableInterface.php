<?php

namespace Concrete\Core\File\Tracker;

use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Statistics\UsageTracker\TrackableInterface;

/**
 * @since 8.0.0
 */
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
