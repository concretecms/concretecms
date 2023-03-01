<?php

namespace Concrete\Core\Announcement\Icon;

interface IconInterface extends \JsonSerializable
{

    public function getElement(): string;

}
