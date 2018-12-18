<?php
namespace Concrete\Core\Logging;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Utility\Service\Text;

class Channels
{

    const CHANNEL_APPLICATION = 'application';
    const CHANNEL_AUTHENTICATION = 'authentication';
    const CHANNEL_EMAIL = 'sent_emails';
    const CHANNEL_EXCEPTIONS = 'exceptions';
    const CHANNEL_SECURITY = 'security';
    const CHANNEL_PACKAGES = 'packages';
    const CHANNEL_SPAM = 'spam';
    const CHANNEL_SITE_ORGANIZATION = 'site_organization';
    const CHANNEL_USERS = 'users';

    public static function getCoreChannels()
    {
        return [
            self::CHANNEL_EMAIL,
            self::CHANNEL_EXCEPTIONS,
            self::CHANNEL_PACKAGES,
            self::CHANNEL_SECURITY,
            self::CHANNEL_AUTHENTICATION,
            self::CHANNEL_SPAM,
            self::CHANNEL_SITE_ORGANIZATION,
            self::CHANNEL_USERS,
        ];
    }

    /**
     * Returns a list of channels that have been used. Requires the database
     *  handler.
     */
    public function getChannels()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make(Connection::class);
        $channels = (array) $db->GetCol('select distinct channel from Logs order by channel asc');
        return $channels;
    }

    public static function getChannelDisplayName($channel)
    {
        $text = new Text();
        switch ($channel) {
            case 'application':
                return tc('Log channel', 'Application');
            case LOG_TYPE_EMAILS:
                return tc('Log channel', 'Sent Emails');
            case LOG_TYPE_EXCEPTIONS:
                return tc('Log channel', 'Exceptions');
            default:
                return tc('Log channel', $text->unhandle($channel));
        }
    }

}



