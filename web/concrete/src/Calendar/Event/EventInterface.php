<?php
namespace Concrete\Core\Calendar\Event;

use Concrete\Core\Foundation\Repetition\RepetitionInterface;

/**
 * A simplified interface for Calendar Events
 *
 * @package Concrete\Core\Calendar
 */
interface EventInterface
{

    public function getCalendarID();
    public function getCalendar();
    public function setCalendar(\Concrete\Core\Calendar\Calendar $calendar);

    /**
     * The identifier, null for unsaved
     *
     * @return string|int|null
     */
    public function getID();

    /**
     * The repetition object
     *
     * @return RepetitionInterface
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
     * @param RepetitionInterface $repetition
     */
    public function setRepetition(RepetitionInterface $repetition);

    /**
     * Save this event
     *
     * @return bool Success / failure
     */
    public function save();

}
