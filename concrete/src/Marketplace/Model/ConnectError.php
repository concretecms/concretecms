<?php

declare(strict_types=1);

namespace Concrete\Core\Marketplace\Model;

/**
 * @readonly
 */
final class ConnectError
{
    /** @var string */
    public $error;
    /** @var int */
    public $code;

    public function __construct(string $error, int $code)
    {
        $this->error = $error;
        $this->code = $code;
    }
}
