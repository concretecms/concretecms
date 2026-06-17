<?php

declare(strict_types=1);

namespace Concrete\Core\Marketplace\Model;

/**
 * @readonly
 */
final class ConnectResult
{
    /** @var string */
    public $private;
    /** @var string */
    public $public;
    /** @var string */
    public $siteId;

    public function __construct(string $private, string $public, string $site_id)
    {
        $this->private = $private;
        $this->public = $public;
        $this->siteId = $site_id;
    }
}
