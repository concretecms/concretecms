<?php

namespace Concrete\Core\Events\Broadcast;

interface BroadcastableEventInterface extends \JsonSerializable
{

    function getName();
    function getBroadcastChannel();
    function jsonSerialize();

}