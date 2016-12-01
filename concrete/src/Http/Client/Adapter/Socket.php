<?php
namespace Concrete\Core\Http\Client\Adapter;

use Zend\Http\Client\Adapter\Proxy as ZendProxySocket;
use Zend\Http\Client\Adapter\Exception\TimeoutException as ZendTimeoutException;
use Concrete\Core\File\Exception\RequestTimeoutException;

class Socket extends ZendProxySocket
{
    /**
     * {@inheritdoc}
     *
     * @see ZendProxySocket::setOptions()
     */
    public function setOptions($options = [])
    {
        $timeout = null;
        if (isset($options['connectiontimeout'])) {
            $timeout = ($timeout === null) ? $options['connectiontimeout'] : max($timeout, $options['connectiontimeout']);
            unset($options['connectiontimeout']);
        }
        if (isset($options['responsetimeout'])) {
            $timeout = ($timeout === null) ? $options['responsetimeout'] : max($timeout, $options['responsetimeout']);
            unset($options['responsetimeout']);
        }
        if ($timeout !== null) {
            $options['timeout'] = $timeout;
        }
        parent::setOptions($options);
    }

    /**
     * {@inheritdoc}
     *
     * @throws RequestTimeoutException
     *
     * @see ZendProxySocket::_checkSocketReadTimeout()
     */
    protected function _checkSocketReadTimeout()
    {
        try {
            parent::_checkSocketReadTimeout();
        } catch (ZendTimeoutException $x) {
            throw new RequestTimeoutException(t('Request timed out.'));
        }
    }
}
