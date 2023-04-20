<?php

namespace Concrete\Core\Announcement\Component;

interface ComponentInterface extends \JsonSerializable
{

    public function getComponent(): string;

    public function getComponentProps(): array;

}
