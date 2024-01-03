<?php

declare(strict_types=1);

namespace Concrete\Core\Marketplace\Model;

/**
 * @readonly
 */
class ConnectResult
{
    public string $private;
    public string $public;
    public string $siteId;

    public function __construct(string $private, string $public, string $site_id)
    {
        $this->private = $private;
        $this->public = $public;
        $this->siteId = $site_id;
    }
}
