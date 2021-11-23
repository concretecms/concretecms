<?php

namespace Concrete\Core\Logging;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Utility\Service\Text;

class Channels
{
    /**
     * Channel identifier: application.
     *
     * @var string
     */
    const CHANNEL_APPLICATION = 'application';

    /**
     * Channel identifier: authentication.
     *
     * @var string
     */
    const CHANNEL_AUTHENTICATION = 'authentication';

    /**
     * Channel identifier: sent emails.
     *
     * @var string
     */
    const CHANNEL_EMAIL = 'sent_emails';

    /**
     * Channel identifier: exceptions.
     *
     * @var string
     */
    const CHANNEL_EXCEPTIONS = 'exceptions';

    /**
     * Channel identifier: security.
     *
     * @var string
     */
    const CHANNEL_SECURITY = 'security';

    /**
     * Channel identifier: packages.
     *
     * @var string
     */
    const CHANNEL_PACKAGES = 'packages';

    /**
     * Channel identifier: spam.
     *
     * @var string
     */
    const CHANNEL_SPAM = 'spam';

    /**
     * Channel identifier: site organization.
     *
     * @var string
     */
    const CHANNEL_SITE_ORGANIZATION = 'site_organization';

    /**
     * Channel identifier: users.
     *
     * @var string
     */
    const CHANNEL_USERS = 'users';

    /**
     * Channel identifier: express.
     *
     * @var string
     */
    const CHANNEL_EXPRESS = 'express';

    /**
     * Channel identifier: content.
     *
     * @var string
     */
    const CHANNEL_CONTENT = 'content';

    /**
     * Channel identifier: permissions.
     *
     * @var string
     */
    const CHANNEL_PERMISSIONS = 'permissions';

    /**
     * Channel identifier: network. Purpose: Log network activity off-site.
     *
     * @var string
     */
    const CHANNEL_NETWORK = 'network';

    /**
     * Channel identifier: operations. Used for routine maintenance operations.
     *
     * @var string
     */
    const CHANNEL_OPERATIONS = 'operations';

    /**
     * Channel identifier: operations. Used for routine maintenance operations.
     *
     * @var string
     */
    const CHANNEL_API = 'api';

    /**
     * Channel identifier: messenger. Used for queue/command/messenger events (not private messages).
     *
     * @var string
     */
    const CHANNEL_MESSENGER = 'messenger';

    /**
     * Channel identifier: board.
     *
     * @var string
     */
    const CHANNEL_BOARD = 'board';

    /**
     * Channel identifier: files.
     *
     * @var string
     */
    const CHANNEL_FILES = 'files';

    /**
     * Channel identifier: all – Do NOT use this to log to. This is a separate system channel that tells configuration
     * that you want to apply all configuration options to all channels, and listen to all of them.
     *
     * @var string
     */
    const META_CHANNEL_ALL = 'all';

    /**
     * Get the list of the core channel.
     *
     * @return string[]
     */
    public static function getCoreChannels()
    {
        return [
            self::CHANNEL_EMAIL,
            self::CHANNEL_EXCEPTIONS,
            self::CHANNEL_PACKAGES,
            self::CHANNEL_SECURITY,
            self::CHANNEL_AUTHENTICATION,
            self::CHANNEL_PERMISSIONS,
            self::CHANNEL_SPAM,
            self::CHANNEL_SITE_ORGANIZATION,
            self::CHANNEL_NETWORK,
            self::CHANNEL_USERS,
            self::CHANNEL_OPERATIONS,
            self::CHANNEL_API,
            self::CHANNEL_FILES,
            self::CHANNEL_CONTENT,
            self::CHANNEL_MESSENGER,
            self::CHANNEL_BOARD,
        ];
    }

    /**
     * Get the list of channels that have been used.
     * Requires the database handler.
     *
     * @return string[]
     */
    public static function getChannels()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make(Connection::class);
        $channels = (array) $db->GetCol('select distinct channel from Logs order by channel asc');

        return $channels;
    }

    /**
     * Get the display name of a channel.
     *
     * @param string $channel
     *
     * @return string
     */
    public static function getChannelDisplayName($channel)
    {
        $text = new Text();
        switch ($channel) {
            case self::CHANNEL_EXPRESS:
                return tc('Log channel', 'Express');
            case self::CHANNEL_APPLICATION:
                return tc('Log channel', 'Application');
            case self::CHANNEL_AUTHENTICATION:
                return tc('Log channel', 'Authentication');
            case self::CHANNEL_EMAIL:
                return tc('Log channel', 'Sent Emails');
            case self::CHANNEL_EXCEPTIONS:
                return tc('Log channel', 'Exceptions');
            case self::CHANNEL_SECURITY:
                return tc('Log channel', 'Security');
            case self::CHANNEL_PACKAGES:
                return tc('Log channel', 'Packages');
            case self::CHANNEL_SPAM:
                return tc('Log channel', 'Spam');
            case self::CHANNEL_SITE_ORGANIZATION:
                return tc('Log channel', 'Site Organization');
            case self::CHANNEL_USERS:
                return tc('Log channel', 'Users');
            case self::CHANNEL_API:
                return tc('Log channel', 'API');
            case self::CHANNEL_BOARD:
                return tc('Log channel', 'BOARD');
            case self::CHANNEL_FILES:
                return tc('Log channel', 'Files');
            default:
                return tc('Log channel', $text->unhandle($channel));
        }
    }
}
