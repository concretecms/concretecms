<?php
namespace Concrete\Core\Url;

/**
 * @since 5.7.4
 */
interface UrlInterface extends \League\Url\UrlInterface
{
    const TRAILING_SLASHES_DISABLED = 0;
    const TRAILING_SLASHES_ENABLED = 1;
    /** @deprecated Trailing slashes are no longer automatically resolved */
    const TRAILING_SLASHES_AUTO = 2;

}
