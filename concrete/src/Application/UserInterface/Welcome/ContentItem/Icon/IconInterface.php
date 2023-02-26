<?php

namespace Concrete\Core\Application\UserInterface\Welcome\ContentItem\Icon;

interface IconInterface extends \JsonSerializable
{

    public function getElement(): string;

}
