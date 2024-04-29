<?php

declare(strict_types=1);

namespace Concrete\Core\Marketplace\Model;

/**
 * @readonly
 */
final class ConnectError
{
    public string $error;
    public int $code;

    public function __construct(string $error, int $code)
    {
        $this->error = $error;
        $this->code = $code;
    }
}
