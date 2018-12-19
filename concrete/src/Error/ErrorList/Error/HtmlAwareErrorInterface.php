<?php

namespace Concrete\Core\Error\ErrorList\Error;

/**
 * @since concrete5 8.5.0a3
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
