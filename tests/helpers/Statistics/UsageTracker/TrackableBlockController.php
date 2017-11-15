<?php

namespace Concrete\TestHelpers\Statistics\UsageTracker;

use Concrete\Core\Block\BlockController;
use Concrete\Core\File\Tracker\FileTrackableInterface;

abstract class TrackableBlockController extends BlockController implements FileTrackableInterface
{
}
