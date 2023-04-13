<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Install\AbstractListablePrecondition;
use Concrete\Core\Install\WebPreconditionInterface;
use Concrete\Core\Url\Resolver\Manager\ResolverManager;

class RequestUrls extends AbstractListablePrecondition implements WebPreconditionInterface
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
        return t('Supports Concrete request URLs');
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
     * @see \Concrete\Core\Install\PreconditionInterface::performCheck()
     */
    public function performCheck()
    {
        return null;
    }

    public function getComponent(): string
    {
        return 'request-urls-precondition';
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();
        $url = (string) $this->resolver->resolve(['/install', 'web_precondition', 'request_urls', '20']);
        $errorMessage = t('Concrete cannot parse the PATH_INFO or ORIG_PATH_INFO information provided by your server.');
        $ajaxFailErrorMessage = json_encode(t('Request failed: unable to verify support for request URLs'));
        $data['ajax_url'] = $url;
        $data['error_message'] = $errorMessage;
        $data['ajax_fail_message'] = $ajaxFailErrorMessage;
        return $data;
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
