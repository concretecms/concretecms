<?php

namespace Concrete\Core\StyleCustomizer\Skin;

interface SkinInterface extends \JsonSerializable
{

    const SKIN_DEFAULT = 'default';

    public function getName(): string;

    public function getIdentifier(): string;

}
