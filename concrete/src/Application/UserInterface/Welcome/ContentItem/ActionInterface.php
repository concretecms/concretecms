<?php

namespace Concrete\Core\Application\UserInterface\Welcome\ContentItem;

interface ActionInterface extends \JsonSerializable
{

    public function getComponent(): string;

    public function getComponentProps(): array;

}
