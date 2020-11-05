<?php

namespace Concrete\Core\Session\Storage;

use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionBagProxy;
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

class LoggedStorage implements SessionStorageInterface, LoggerAwareInterface
{

    use LoggerAwareTrait;

    /**
     * The storage instance we're wrapping / adding logging to
     *
     * @var \Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface
     */
    protected $wrappedStorage;

    public function __construct(SessionStorageInterface $wrappedStorage)
    {
        $this->wrappedStorage = $wrappedStorage;
    }

    /**
     * Declare our logging channel as "Security"
     *
     * @return string
     */
    public function getLoggerChannel()
    {
        return Channels::CHANNEL_SECURITY;
    }

    /**
     * Log details if possible
     *
     * @param $message
     * @param array $context
     */
    protected function log($level, $message, array $context = [])
    {
        if ($this->logger) {
            $metadata = $this->getMetadataBag();

            $context['metadata'] = [
                'created' => $metadata->getCreated(),
                'lifetime' => $metadata->getLifetime(),
                'lastused' => $metadata->getLastUsed(),
                'name' => $metadata->getName(),
            ];

            if ($this->isStarted()) {
                $attributes = $this->getBag('attributes');

                if ($attributes instanceof SessionBagProxy) {
                    $attributes = $attributes->getBag();
                }

                if ($attributes instanceof AttributeBagInterface) {
                    $context['metadata']['uID'] = $attributes->get('uID', null);
                    $context['metadata']['uGroups'] = array_values($attributes->get('uGroups', []));
                }
            }

            $this->logger->log($level, $message, $context);
        }
    }

    /**
     * Add info level logs
     *
     * @param $message
     * @param array $context
     */
    protected function logInfo($message, array $context = [])
    {
        return $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Add debug level logs
     *
     * @param $message
     * @param array $context
     */
    protected function logDebug($message, array $context = [])
    {
        return $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Starts the session.
     *
     * @return bool True if started
     *
     * @throws \RuntimeException if something goes wrong starting the session
     */
    public function start()
    {
        $this->logDebug('Session starting.');

        return $this->wrappedStorage->start();
    }

    /**
     * Checks if the session is started.
     *
     * @return bool True if started, false otherwise
     */
    public function isStarted()
    {
        return $this->wrappedStorage->isStarted();
    }

    /**
     * Returns the session ID.
     *
     * @return string The session ID or empty
     */
    public function getId()
    {
        return $this->wrappedStorage->getId();
    }

    /**
     * Sets the session ID.
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->logDebug('Modifying session ID');

        return $this->wrappedStorage->setId($id);
    }

    /**
     * Returns the session name.
     *
     * @return mixed The session name
     */
    public function getName()
    {
        return $this->wrappedStorage->getName();
    }

    /**
     * Sets the session name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        return $this->wrappedStorage->setName($name);
    }

    /**
     * Regenerates id that represents this storage.
     *
     * This method must invoke session_regenerate_id($destroy) unless
     * this interface is used for a storage object designed for unit
     * or functional testing where a real PHP session would interfere
     * with testing.
     *
     * Note regenerate+destroy should not clear the session data in memory
     * only delete the session data from persistent storage.
     *
     * Care: When regenerating the session ID no locking is involved in PHP's
     * session design. See https://bugs.php.net/bug.php?id=61470 for a discussion.
     * So you must make sure the regenerated session is saved BEFORE sending the
     * headers with the new ID. Symfony's HttpKernel offers a listener for this.
     * See Symfony\Component\HttpKernel\EventListener\SaveSessionListener.
     * Otherwise session data could get lost again for concurrent requests with the
     * new ID. One result could be that you get logged out after just logging in.
     *
     * @param bool $destroy Destroy session when regenerating?
     * @param int $lifetime Sets the cookie lifetime for the session cookie. A null value
     *                       will leave the system settings unchanged, 0 sets the cookie
     *                       to expire with browser session. Time is in seconds, and is
     *                       not a Unix timestamp.
     *
     * @return bool True if session regenerated, false if error
     *
     * @throws \RuntimeException If an error occurs while regenerating this storage
     */
    public function regenerate($destroy = false, $lifetime = null)
    {
        $this->logInfo('Regenerating session', ['destroy' => $destroy, 'lifetime' => $lifetime]);

        return $this->wrappedStorage->regenerate($destroy, $lifetime);
    }

    /**
     * Force the session to be saved and closed.
     *
     * This method must invoke session_write_close() unless this interface is
     * used for a storage object design for unit or functional testing where
     * a real PHP session would interfere with testing, in which case
     * it should actually persist the session data if required.
     *
     * @throws \RuntimeException if the session is saved without being started, or if the session
     *                           is already closed
     */
    public function save()
    {
        $this->logDebug('Session saving');
        return $this->wrappedStorage->save();
    }

    /**
     * Clear all session data in memory.
     */
    public function clear()
    {
        $this->logInfo('Clearing Session.');

        $metadata = $this->getMetadataBag();
        $lifetime = $metadata->getLifetime();
        $lastUsed = $metadata->getLastUsed();

        // If the existing session has expired on its own
        if ($lifetime && time() > $lastUsed + $lifetime) {
            $this->logInfo('Session expired.');
        }

        return $this->wrappedStorage->clear();
    }

    /**
     * Gets a SessionBagInterface by name.
     *
     * @param string $name
     *
     * @return SessionBagInterface
     *
     * @throws \InvalidArgumentException If the bag does not exist
     */
    public function getBag($name)
    {
        return $this->wrappedStorage->getBag($name);
    }

    /**
     * Registers a SessionBagInterface for use.
     */
    public function registerBag(SessionBagInterface $bag)
    {
        return $this->wrappedStorage->registerBag($bag);
    }

    /**
     * @return MetadataBag
     */
    public function getMetadataBag()
    {
        return $this->wrappedStorage->getMetadataBag();
    }
}
