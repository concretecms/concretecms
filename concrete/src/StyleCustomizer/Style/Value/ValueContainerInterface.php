<?php

namespace Concrete\Core\StyleCustomizer\Style\Value;

interface ValueContainerInterface
{

    public function getSubStyleValues(): array;

    public function hasSubStyleValues(): bool;

}
