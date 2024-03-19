<?php

declare(strict_types=1);

namespace Concrete\Core\Marketplace\Model;

/**
 * @readonly
 */
class ValidateResult
{
    public bool $valid;
    public string $site;

    public function __construct(bool $valid, string $site)
    {
        $this->valid = $valid;
        $this->site = $site;
    }
}
