<?php

namespace Concrete\TestHelpers\Statistics\UsageTracker;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Statistics\UsageTracker\TrackableInterface;

/**
 * A block controller that declares compatibility with the statistics tracker via the
 * plain TrackableInterface only (as opposed to FileTrackableInterface, which extends it).
 *
 * The stack proxy block controller (Concrete\Block\CoreStackDisplay\Controller) is the
 * real-world example of this: it implements TrackableInterface but not FileTrackableInterface,
 * so BlockController must not gate its tracking hooks on FileTrackableInterface alone.
 */
class PlainTrackableBlockController extends BlockController implements TrackableInterface
{
}