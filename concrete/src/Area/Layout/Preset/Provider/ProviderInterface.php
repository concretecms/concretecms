<?php
namespace Concrete\Core\Area\Layout\Preset\Provider;

/**
 * @since 5.7.5
 */
interface ProviderInterface
{
    public function getPresets();
    public function getName();
}
