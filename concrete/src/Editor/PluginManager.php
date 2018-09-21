<?php

namespace Concrete\Core\Editor;

use Exception;

class PluginManager
{
    /**
     * The list of available plugins.
     *
     * @var \Concrete\Core\Editor\Plugin[]
     */
    protected $plugins = [];

    /**
     * The handles of the selected plugins.
     *
     * @var string[]
     */
    protected $selectedPlugins = [];

    /**
     * Register a new plugin, adding it to the list of available plugins.
     *
     * @param \Concrete\Core\Editor\Plugin|string $plugin the plugin to register (or its handle)
     * @param string|null $name the name of the plugin (if $plugin is the plugin handle
     *
     * @throws \Exception throws an Exception if $plugin is the plugin handle, but $name is empty
     */
    public function register($plugin, $name = null)
    {
        if ($plugin instanceof Plugin) {
            $key = $plugin->getKey();
            $p = $plugin;
        } else {
            if (!$name) {
                throw new Exception(t('You must specify a plugin key and name.'));
            }
            $key = $plugin;
            $p = new Plugin();
            $p->setKey($key);
            $p->setName($name);
        }
        $this->plugins[$key] = $p;
    }

    /**
     * Get the list of available plugins.
     *
     * @return \Concrete\Core\Editor\Plugin[]
     */
    public function getAvailablePlugins()
    {
        return $this->plugins;
    }

    /**
     * Check if a plugin is selected (enabled).
     *
     * @param \Concrete\Core\Editor\Plugin|string $key the plugin to be checked (or its handle)
     *
     * @return bool
     */
    public function isSelected($key)
    {
        $key = $key instanceof Plugin ? $key->getKey() : $key;

        return in_array($key, $this->selectedPlugins);
    }

    /**
     * Check if a plugin is registered (available).
     *
     * @param \Concrete\Core\Editor\Plugin|string $key the plugin to be checked (or its handle)
     *
     * @return bool
     */
    public function isAvailable($key)
    {
        $key = $key instanceof Plugin ? $key->getKey() : $key;

        return array_key_exists($key, $this->plugins);
    }

    /**
     * Mark a plugin as selected (enabled).
     *
     * @param \Concrete\Core\Editor\Plugin|\Concrete\Core\Editor\Plugin[]|string|string[] $keyOrKeys the plugin(s) to be marked as selected (or their handles)
     */
    public function select($keyOrKeys)
    {
        if ($keyOrKeys instanceof Plugin) {
            $keys = [$keyOrKeys->getKey()];
        } elseif (is_array($keyOrKeys)) {
            $keys = [];
            foreach ($keyOrKeys as $kok) {
                $keys[] = $kok instanceof Plugin ? $kok->getKey() : $kok;
            }
        } else {
            $keys = [$keyOrKeys];
        }

        $this->selectedPlugins = array_unique(array_merge($this->selectedPlugins, $keys));
    }

    /**
     * Mark a plugin as not selected (disabled).
     *
     * @param \Concrete\Core\Editor\Plugin|\Concrete\Core\Editor\Plugin[]|string|string[] $keyOrKeys the plugin(s) to be marked as unselected (or their handles)
     */
    public function deselect($keyOrKeys)
    {
        if ($keyOrKeys instanceof Plugin) {
            $keys = [$keyOrKeys->getKey()];
        } elseif (is_array($keyOrKeys)) {
            $keys = [];
            foreach ($keyOrKeys as $kok) {
                $keys[] = $kok instanceof Plugin ? $kok->getKey() : $kok;
            }
        } else {
            $keys = [$keyOrKeys];
        }
        $this->selectedPlugins = array_diff($this->selectedPlugins, $keys);
    }

    /**
     * Get the list of selected (enabled) plugin handles.
     *
     * @return string[]
     */
    public function getSelectedPlugins()
    {
        $manager = $this;

        return array_filter($this->selectedPlugins, function ($plugin) use ($manager) {
            return $manager->isAvailable($plugin);
        });
    }

    /**
     * Get the list of selected (enabled) plugin instances.
     *
     * @return \Concrete\Core\Editor\Plugin[]
     */
    public function getSelectedPluginObjects()
    {
        $selected = [];
        $plugins = $this->getSelectedPlugins();
        foreach ($plugins as $key) {
            $selected[] = $this->plugins[$key];
        }

        return $selected;
    }

    /**
     * @deprecated Use the select() method
     *
     * @param mixed $plugins
     */
    public function selectMultiple($plugins)
    {
        $this->select($plugins);
    }
}
