<?php
namespace Concrete\Core\Calendar\Event;

/**
 * A simplified interface for Calendar Events
 *
 * @package Concrete\Core\Calendar
 */
interface EventInterface
{

    /**
     * The repetition object
     *
     * @return Repetition
     */
    public function getRepetition();

    /**
     * Get the event name
     *
     * @return string
     */
    public function getName();

    /**
     * Set the event name
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get the event description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set the event description
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * Set the new repetition object
     *
     * @param Repetition $repetition
     */
    public function setRepetition(Repetition $repetition);

    /**
     * Save this event
     *
     * @return bool Success / failure
     */
    public function save();

}
