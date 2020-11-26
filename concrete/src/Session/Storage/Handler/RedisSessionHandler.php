<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Can be found in concrete/vendor/symfony/http-foundation
 */

namespace Concrete\Core\Session\Storage\Handler;

use SessionHandler;

/**
 * Redis based session storage handler based on the Redis class
 * provided by the PHP redis extension.
 *
 * @author Dalibor Karlović <dalibor@flexolabs.io>
 * modified by Derek Cameron <derek@concrete5.co.jp> for concrete5 from symfony 4.1
 */
class RedisSessionHandler extends SessionHandler
{

    private $redis;

    /**
     * @var string Key prefix for shared environments
     */
    private $prefix;

    /**
     * List of available options:
     *  * prefix: The prefix to use for the keys in order to avoid collision on the Redis server.
     *
     * @param \Redis|\RedisArray|\RedisCluster|\Predis\Client $redis
     * @param array                                                      $options An associative array of options
     *
     * @throws \InvalidArgumentException When unsupported client or options are passed
     */
    public function __construct($redis, array $options = array())
    {
        if (
            !$redis instanceof \Redis &&
            !$redis instanceof \RedisArray &&
            !$redis instanceof \RedisCluster &&
            !$redis instanceof \Predis\Client
        ) {
            throw new \InvalidArgumentException(sprintf('%s() expects parameter 1 to be Redis, RedisArray, RedisCluster or Predis\Client, %s given', __METHOD__, \is_object($redis) ? \get_class($redis) : \gettype($redis)));
        }

        if ($diff = array_diff(array_keys($options), array('prefix'))) {
            throw new \InvalidArgumentException(sprintf('The following options are not supported "%s"', implode(', ', $diff)));
        }

        $this->redis = $redis;
        $this->prefix = $options['prefix'] ? $options['prefix'] : 'sf_s';
    }

    /**
     * {@inheritdoc}
     */
    protected function doRead($sessionId)
    {
        return $this->redis->get($this->prefix.$sessionId) ?: '';
    }

    /**
     * {@inheritdoc}
     */
    public function read($session_id)
    {
        return $this->doRead($session_id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doWrite($sessionId, $data)
    {
        $result = $this->redis->setex($this->prefix.$sessionId, (int) ini_get('session.gc_maxlifetime'), $data);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function write($session_id, $session_data)
    {
        return $this->doWrite($session_id, $session_data);
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($session_id)
    {
        return $this->doDestroy($session_id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doDestroy($sessionId)
    {
        $this->redis->del($this->prefix.$sessionId);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function updateTimestamp($sessionId, $data)
    {
        return (bool) $this->redis->expire($this->prefix.$sessionId, (int) ini_get('session.gc_maxlifetime'));
    }
}