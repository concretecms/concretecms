<?php
namespace Concrete\Core\Area\Layout\Preset\Provider;

use Concrete\Core\Area\Layout\Preset\InvalidPresetException;
use Concrete\Core\Area\Layout\Preset\PresetInterface;

class Manager
{
    protected $providers = array();

    public function register(ProviderInterface $provider)
    {
        $this->providers[$provider->getName()] = $provider;
    }

    public function unregister($nameOrObject)
    {
        $plugin = ($nameOrObject instanceof ProviderInterface) ? $nameOrObject : $this->providers[$nameOrObject];
        unset($this->providers[$plugin->getName()]);
    }

    public function getByName($name)
    {
        return $this->providers[$name];
    }

    public function getProviders()
    {
        return $this->providers;
    }

    public function getPresets()
    {
        $presets = array();
        foreach ($this->providers as $provider) {
            $presets = array_merge($presets, $provider->getPresets());
        }
        array_map(function ($preset) {
            if (!($preset instanceof PresetInterface)) {
                throw new InvalidPresetException(t('Items returned by getPresets() must implement the PresetInterface'));
            }
        }, $presets);

        return $presets;
    }

    public function getPresetByIdentifier($identifier)
    {
        $results = array_filter($this->getPresets(), function ($preset) use ($identifier) {
            return $preset->getIdentifier() == $identifier;
        });
        $results = array_values($results);

        return $results[0];
    }
}
