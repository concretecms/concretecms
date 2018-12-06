<?php
namespace Concrete\Core\Logging;

class Channels
{

    const CHANNEL_APPLICATION = 'application';
    const CHANNEL_EMAIL = 'sent_emails';
    const CHANNEL_EXCEPTIONS = 'exceptions';
    const CHANNEL_SECURITY = 'security';

    public static function getCoreChannels()
    {
        return [
            self::CHANNEL_EMAIL,
            self::CHANNEL_EXCEPTIONS,
            self::CHANNEL_SECURITY,
        ];
    }

}



