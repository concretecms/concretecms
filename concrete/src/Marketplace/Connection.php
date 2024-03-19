<?php

declare(strict_types=1);

namespace Concrete\Core\Marketplace;

final class Connection implements ConnectionInterface
{
    private string $public;
    private string $private;

    public function __construct(
        string $public,
        #[\SensitiveParameter]
        string $private
    ) {
        $this->public = $public;
        $this->private = $private;
    }

    public function getPrivate(): string
    {
        return $this->private;
    }

    public function getPublic(): string
    {
        return $this->public;
    }
}
