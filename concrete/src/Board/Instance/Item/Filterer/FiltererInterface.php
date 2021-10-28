<?php

namespace Concrete\Core\Board\Instance\Item\Filterer;

use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;
use Concrete\Core\Entity\Board\InstanceItem;

defined('C5_EXECUTE') or die("Access Denied.");

interface FiltererInterface
{

    /**
     * Does our current configuration support filtering? If not, let us know here for performance reasons, to skip
     * a lot of meaningless checks.
     *
     * @param Configuration $configuration
     * @return bool
     */
    public function configurationSupportsFiltering(Configuration $configuration): bool;

    /**
     * Should this current instance item be removed from the full item pool?
     *
     * IMPORTANT NOTE: The $items passed to this method are ALL items in the data pool, not just of the current
     * configuration. That means if you have pages and events in the same data pool, pages will be passed to the
     * event configuration, etc... That's why it's important ot check the data source within your filterer to
     * ensure you're operating on the right type of object.
     *
     * Note, this returns a completely new array of instance items. The filterer is responsible for populating it.
     * @param Configuration $configuration
     * @param InstanceItem[] $items
     * @return InstanceItem[]
     */
    public function filterItems(Configuration $configuration, array $items): array;

}

