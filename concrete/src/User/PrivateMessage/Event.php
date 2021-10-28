<?php
namespace Concrete\Core\User\PrivateMessage;

class Event
{
    protected $pm;

    public function __construct(PrivateMessage $pm)
    {
        $this->pm = $pm;
    }

    public function getPrivateMessageObject()
    {
        return $this->pm;
    }
}
