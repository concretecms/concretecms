<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

interface EventInterface
{

    public function getEvent(): string;

    public function getData(): array;

}

