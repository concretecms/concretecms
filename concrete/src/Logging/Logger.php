<?php
namespace Concrete\Core\Logging;

use Concrete\Core\Logging\Handler\DatabaseHandler;
use Monolog\Logger as MonologLogger;

/**
 * @deprecated - Use the LoggerFactory to create this. Calling this directly will not load any handlers.
 * Note – using LoggerFactory will not create this class, it will simply create Monolog logger instances.
 * This class purely exists for backward compatibility purposes.
 * Class Logger
 */
class Logger extends MonologLogger
{

    /**
     * Deprecated. Use Concrete\Core\Logging\Channels::getChannels() instead.
     */
    public function getChannels()
    {
        return Channels::getChannels();
    }

    /**
     * Deprecated. Use Concrete\Core\Logging\Handler\DatabaseHandler::clearAll() instead.
     */
    public static function clearAll()
    {
        return DatabaseHandler::clearAll();
    }

    /**
     * Deprecated. Use Concrete\Core\Logging\Handler\DatabaseHandler::clearAll() instead.
     *
     * @param string $channel
     */
    public static function clearByChannel($channel)
    {
        return DatabaseHandler::clearByChannel($channel);
    }

    /**
     * Deprecated. Use Concrete\Core\Logging\Levels::getLevelDisplayName() instead.
     *
     * @param int $level
     * @return string
     */
    public static function getLevelDisplayName($level)
    {
        return Levels::getLevelDisplayName($level);
    }

    /**
     * Deprecated. Use Concrete\Core\Logging\Channels::getChannelDisplayName() instead.
     *
     * @param string $channel
     * @return string
     */
    public static function getChannelDisplayName($channel)
    {
        return Channels::getChannelDisplayName($channel);
    }



}
