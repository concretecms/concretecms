<?php

namespace Concrete\Core\Error\ErrorList\Error;

/**
 * @since 8.4.4
 */
interface HtmlAwareErrorInterface extends ErrorInterface
{
    /**
     * Does the message contain an HTML-formatted string?
     *
     * @return bool
     */
    public function messageContainsHtml();
}
