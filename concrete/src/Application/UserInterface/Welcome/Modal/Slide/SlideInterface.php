<?php

namespace Concrete\Core\Application\UserInterface\Welcome\Modal\Slide;

interface SlideInterface extends \JsonSerializable
{

    public function getComponent(): string;

    public function getComponentProps(): array;

}
