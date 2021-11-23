<?php
namespace Concrete\Core\Area\Layout\Preset\Provider;

use Concrete\Core\Area\Layout\Preset\InvalidPresetException;
use Concrete\Core\Area\Layout\Preset\PresetInterface;

class Manager
{
    protected $providers = array();

    protected $presets;

    public function register(ProviderInterface $provider)
    {
        $this->providers[$provider->getName()] = $provider;
        $this->reset();
    }

    public function reset()
    {
        unset($this->presets);
    }

    public function unregister($nameOrObject)
    {
        $plugin = ($nameOrObject instanceof ProviderInterface) ? $nameOrObject : $this->providers[$nameOrObject];
        unset($this->providers[$plugin->getName()]);
        $this->reset();
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
        if (!isset($this->presets)) {
            $this->presets = array();
            foreach ($this->providers as $provider) {
                $this->presets = array_merge($this->presets, $provider->getPresets());
            }
            array_map(function ($preset) {
                if (!($preset instanceof PresetInterface)) {
                    throw new InvalidPresetException(t('Items returned by getPresets() must implement the PresetInterface'));
                }
            }, $this->presets);
        }
        return $this->presets;
    }

    public function getPresetByIdentifier($identifier)
    {
        $results = array_filter($this->getPresets(), function ($preset) use ($identifier) {
            return $preset->getIdentifier() == $identifier;
        });
        $results = array_values($results);

        if (!empty($results[0])) {
            return $results[0];
        }
        return null;
    }
}
