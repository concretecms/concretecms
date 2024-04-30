<?php

namespace Concrete\Core\Marketplace;

interface ConnectionInterface
{
    public function getPrivate(): string;

    public function getPublic(): string;
}
