<?php

namespace Concrete\Core\Logging\Entry;

/**
 * An interface for making complex log entries
 */
interface EntryInterface
{

    /**
     * Convert this entry into a string that can be inserted into the log
     *
     * Ex: "Created a new user"
     *
     * @return string
     */
    public function getMessage();

    /**
     * Get the added context for the log entry
     *
     * Ex: ["username": "...", "email": "...", "id": "...", "created_by": "..."]
     *
     * @return array
     */
    public function getContext();

}
