<?php

namespace Concrete\Core\Messenger\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

/**
 * Messages with this stamp applied will never be sent to an async sender. Useful for working with messages consumed
 * via the console.
 */
class SkipSendersStamp implements StampInterface
{



}
