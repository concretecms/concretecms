<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Install\WebPreconditionInterface;
use Concrete\Core\Url\Resolver\Manager\ResolverManager;

class RequestUrls implements WebPreconditionInterface
{
    /**
     * The URL resolver.
     *
     * @var ResolverManager
     */
    protected $resolver;

    /**
     * Initialize the instance.
     *
     * @param ResolverManager $resolver The URL resolver
     */
    public function __construct(ResolverManager $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getName()
     */
    public function getName()
    {
        return t('Supports concrete5 request URLs');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'request_urls';
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
        $url = json_encode((string) $this->resolver->resolve(['/install', 'web_precondition', 'request_urls', '20']));
        $errorMessage = json_encode(t('concrete5 cannot parse the PATH_INFO or ORIG_PATH_INFO information provided by your server.'));
        $myIdentifier = json_encode($this->getUniqueIdentifier());

        return <<<EOT
<script>
$(document).ready(function() {
    $.ajax({
        cache: false,
        dataType: 'json',
        method: 'GET',
        url: {$url}
    })
    .done(function(data) {
        if (data.response === 400) {
            setWebPreconditionResult({$myIdentifier}, true);
        } else {
            setWebPreconditionResult({$myIdentifier}, false, {$errorMessage});
        }
    })
    .fail(function(xhr, textStatus, errorThrown) {
        setWebPreconditionResult({$myIdentifier}, false, {$errorMessage});
    });
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
        $i = is_int($argument) || is_string($argument) && is_numeric($argument) ? (int) $argument : null;

        return [
            'response' => $i === null ? null : $i * $i,
        ];
    }
}
