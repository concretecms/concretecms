<?php

namespace Concrete\Core\Events\Broadcast;

/**
 * Classes that implement this interface are responsible for taking
 * the kinds of objects they care about turning them into JSON that
 * can be sent to a broadcast platform like redis.
 * Interface BroadcastSerializer
 * @package Concrete\Core\Events
 */
interface BroadcastSerializer
{

    /**
     * Returns a JSON string for the object.
     * @param $mixed
     * @return string
     */
    public function serializeForBroadcast($mixed);

}