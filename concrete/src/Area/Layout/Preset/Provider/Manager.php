<?php
namespace Concrete\Core\Area\Layout\Preset\Provider;

use Concrete\Core\Area\Layout\Preset\InvalidPresetException;
use Concrete\Core\Area\Layout\Preset\PresetInterface;

class Manager
{
    /**
     * The keyed (by name) list of registered providers.
     *
     * @var \Concrete\Core\Area\Layout\Preset\Provider\ProviderInterface[]
     */
    protected $providers = [];

    /**
     * The list of loaded presets.
     *
     * @var \Concrete\Core\Area\Layout\Preset\PresetInterface[]|null
     */
    protected $presets;

    /**
     * Register a provider.
     */
    public function register(ProviderInterface $provider)
    {
        $this->providers[$provider->getName()] = $provider;
        $this->reset();
    }

    /**
     * Reset the list of loaded presets.
     */
    public function reset()
    {
        $this->presets = null;
    }

    /**
     * Unregister a provider.
     *
     * @param \Concrete\Core\Area\Layout\Preset\Provider\ProviderInterface|string $nameOrObject
     */
    public function unregister($nameOrObject)
    {
        if ($nameOrObject instanceof ProviderInterface) {
            $name = $nameOrObject->getName();
        } else {
            $name = (string) $nameOrObject;
        }
        unset($this->providers[$name]);
        $this->reset();
    }

    /**
     * Get a provider by its name.
     *
     * @param string $name
     *
     * @return \Concrete\Core\Area\Layout\Preset\Provider\ProviderInterface|null
     */
    public function getByName($name)
    {
        return $this->providers[$name] ?? null;
    }

    /**
     * Get the keyed (by name) list of registered providers.
     *
     * @return \Concrete\Core\Area\Layout\Preset\Provider\ProviderInterface[]
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * Get all the presets.
     *
     * @throws \Concrete\Core\Area\Layout\Preset\InvalidPresetException
     *
     * @return \Concrete\Core\Area\Layout\Preset\PresetInterface[]|null
     */
    public function getPresets()
    {
        if ($this->presets !== null) {
            return $this->presets;
        }
        $presets = [];
        foreach ($this->providers as $provider) {
            $presets = array_merge($presets, $provider->getPresets());
        }
        array_map(
            static function ($preset) {
                if (!($preset instanceof PresetInterface)) {
                    throw new InvalidPresetException(t('Items returned by getPresets() must implement the PresetInterface'));
                }
             },
             $presets
        );
        $this->presets = $presets;

        return $this->presets;
    }

    /**
     * Get a preset by its identifier.
     *
     * @param string $identifier
     *
     * @return \Concrete\Core\Area\Layout\Preset\PresetInterface|null
     */
    public function getPresetByIdentifier($identifier)
    {
        $results = array_filter(
            $this->getPresets(),
            static function (PresetInterface $preset) use ($identifier): bool {
                return $preset->getIdentifier() == $identifier;
            }
        );

        return array_pop($results);
    }
}
