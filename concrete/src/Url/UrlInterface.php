<?php
namespace Concrete\Core\Url;

/**
 * @since 5.7.4
 */
interface UrlInterface extends \League\Url\UrlInterface
{
    /**
     * @since 5.7.4.2
     */
    const TRAILING_SLASHES_DISABLED = 0;
    /**
     * @since 5.7.4.2
     */
    const TRAILING_SLASHES_ENABLED = 1;
    /** @deprecated Trailing slashes are no longer automatically resolved
     * @since 5.7.4.2
     */
    const TRAILING_SLASHES_AUTO = 2;

}
