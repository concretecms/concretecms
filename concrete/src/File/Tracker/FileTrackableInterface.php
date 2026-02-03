<?php

namespace Concrete\Core\File\Tracker;

use Concrete\Core\Statistics\UsageTracker\TrackableInterface;

/**
 * Implement this interface to declare files usage.
 */
interface FileTrackableInterface extends TrackableInterface
{
    /**
     * Get the used files.
     *
     * @return array An array of File entities, file UUIDs or file IDs
     */
    public function getUsedFiles();
}
