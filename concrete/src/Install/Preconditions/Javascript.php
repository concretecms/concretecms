<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Install\PreconditionResult;
use Concrete\Core\Install\WebPreconditionInterface;

class Javascript implements WebPreconditionInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getName()
     */
    public function getName()
    {
        return t('JavaScript Enabled');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'javascript';
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
        return PreconditionResult::STATE_FAILED;
    }

    /**
     * {@inheritdoc}
     *
     * @see WebPreconditionInterface::getInitialMessage()
     */
    public function getInitialMessage()
    {
        return t('Please enable JavaScript in your browser.');
    }

    /**
     * {@inheritdoc}
     *
     * @see WebPreconditionInterface::getHtml()
     */
    public function getHtml()
    {
        $myIdentifier = json_encode($this->getUniqueIdentifier());

        return <<<EOT
<script>
$(document).ready(function() {
    setWebPreconditionResult({$myIdentifier}, true);
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
