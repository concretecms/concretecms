<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Install\WebPreconditionInterface;

class Cookies implements WebPreconditionInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getName()
     */
    public function getName()
    {
        return t('Cookies Enabled');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'cookies';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::isOptional()
     */
    public function isOptional()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see WebPreconditionInterface::getInitialState()
     */
    public function getInitialState()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @see WebPreconditionInterface::getInitialMessage()
     */
    public function getInitialMessage()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     *
     * @see WebPreconditionInterface::getHtml()
     */
    public function getHtml()
    {
        $errorMessage = json_encode(t('Cookies must be enabled in your browser to install concrete5.'));
        $myIdentifier = json_encode($this->getUniqueIdentifier());

        return <<<EOT
<script>
$(document).ready(function() {
    function check() {
        if (typeof navigator.cookieEnabled === 'boolean') {
            return navigator.cookieEnabled;
        }
        var COOKIE_NAME = 'CONCRETE5_INSTALL_TEST', COOKIE_VALUE = 'ok_' + Math.random();
        document.cookie = COOKIE_NAME + '=' + COOKIE_VALUE;
        if (document.cookie.indexOf(COOKIE_NAME + '=' + COOKIE_VALUE) < 0) {
            return false;
        }
        document.cookie = COOKIE_NAME + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT';
        return true;
    }
    if (check()) {
        setWebPreconditionResult({$myIdentifier}, true);
    } else {
        setWebPreconditionResult({$myIdentifier}, false, {$errorMessage});
    }
});
</script>
EOT
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::performCheck()
     */
    public function performCheck()
    {
        throw new UserMessageException('This precondition does not have PHP checks');
    }

    /**
     * {@inheritdoc}
     *
     * @see WebPreconditionInterface::getAjaxAnswer()
     */
    public function getAjaxAnswer($argument)
    {
        throw new UserMessageException('This precondition does not have PHP checks');
    }
}
