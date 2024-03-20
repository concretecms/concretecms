<?php

declare(strict_types=1);

namespace Concrete\Core\Marketplace\Update;

interface UpdatedFieldInterface
{
    public const FIELD_NAME = 'name';

    public function getName(): string;
    public function getData();
}
