<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Install\WebPreconditionInterface;
use Concrete\Core\Url\Resolver\Manager\ResolverManager;

class PrettyUrls implements WebPreconditionInterface
{
    /**
     * The URL resolver.
     *
     * @var ResolverManager
     */
    protected $resolver;

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * Initialize the instance.
     *
     * @param ResolverManager $resolver The URL resolver
     */
    public function __construct(ResolverManager $resolver, Repository $config)
    {
        $this->resolver = $resolver;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getName()
     */
    public function getName()
    {
        return t(/* i18n: %s is "index.php" */'Support for URLs without %s ("pretty URLs")', \DISPATCHER_FILENAME);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'pretty_urls';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::isOptional()
     */
    public function isOptional()
    {
        return true;
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
        $url = $this->config->withKey('concrete.seo.url_rewriting', true, function (): string {
            return $this->config->withKey('concrete.seo.url_rewriting_all', true, function (): string {
                return (string) $this->resolver->resolve(['/install', 'web_precondition', $this->getUniqueIdentifier(), 'ping']);
            });
        });
        $url = json_encode($url);
        $errorMessage = json_encode(t('It seems seems your web server does not support pretty URLs.'));
        $myIdentifier = json_encode($this->getUniqueIdentifier());
        $isOptional = json_encode($this->isOptional());

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
        if (data && data.response === 'pong') {
            $('form#continue-to-installation').append('<input type="hidden" name="prettyURLsSupported" value="1" />');
            setWebPreconditionResult({$myIdentifier}, true);
        } else {
            setWebPreconditionResult({$myIdentifier}, false, {$errorMessage}, {$isOptional});
        }
    })
    .fail(function(xhr, textStatus, errorThrown) {
        setWebPreconditionResult({$myIdentifier}, false, {$errorMessage}, {$isOptional});
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
        return [
            'response' => $argument === 'ping' ? 'pong' : '',
        ];
    }
}
