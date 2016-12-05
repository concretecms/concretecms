<?php
namespace Concrete\Core\Http\Client\Adapter;

use Zend\Http\Client\Adapter\Proxy as ZendProxySocket;
use Zend\Http\Client\Adapter\Exception\TimeoutException as ZendTimeoutException;
use Zend\Http\Client\Adapter\Exception\RuntimeException as ZendRumtimeException;
use Concrete\Core\File\Exception\RequestTimeoutException;

class Socket extends ZendProxySocket
{
    /**
     * {@inheritdoc}
     *
     * @see ZendProxySocket::connect()
     */
    public function connect($host, $port = 80, $secure = false)
    {
        $timeout = $this->config['timeout'] ? $this->config['timeout'] : null;
        if (isset($this->config['connectiontimeout'])) {
            $this->config['timeout'] = $this->config['connectiontimeout'];
        }
        parent::connect($host, $port, $secure);
        $this->config['timeout'] = $timeout;
        if (isset($this->config['executetimeout'])) {
            if (!stream_set_timeout($this->socket, (int) $this->config['executetimeout'])) {
                throw new ZendRumtimeException('Unable to set the connection timeout');
            }
        }
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
