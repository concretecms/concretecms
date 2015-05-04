<?php
namespace Concrete\Core\Editor;

class PluginManager
{

    protected $plugins = array();
    protected $selectedPlugins = array();

    public function register($plugin, $name = null)
    {
        if (!($plugin instanceof Plugin)) {
            if (!$name) {
                throw new \Exception(t('You must specify a plugin key and name.'));
            } else {
                $p = new Plugin();
                $p->setKey($plugin);
                $p->setName($name);
                $key = $plugin;
            }
        } else {
            $p = $plugin;
            $key = $plugin->getKey();
        }

        $this->plugins[$key] = $p;
    }

    public function getAvailablePlugins()
    {
        return $this->plugins;
    }

    public function isSelected($key)
    {
        $key = ($key instanceof Plugin) ? $key->getKey() : $key;
        return in_array($key, $this->selectedPlugins);
    }

    public function isAvailable($key)
    {
        $key = ($key instanceof Plugin) ? $key->getKey() : $key;
        return array_key_exists($key, $this->plugins);
    }

    public function select($key)
    {
        if (!in_array($key, $this->selectedPlugins)) {
            $this->selectedPlugins[] = $key;
        }
    }

    public function selectMultiple($plugins)
    {
        foreach($plugins as $key)
        {
            $this->select($key);
        }
    }

    public function getSelectedPlugins()
    {
        $manager = $this;
        return array_filter($this->selectedPlugins, function($plugin) use ($manager) {
            return $manager->isAvailable($plugin);
        });
    }

    /**
     * returns an array of selected plug-in objects, filtering out those that aren't available
     * @return array
     */
    public function getSelectedPluginObjects()
    {
        $selected = array();
        $plugins = $this->getSelectedPlugins();
        foreach($plugins as $key) {
            $selected[] = $this->plugins[$key];
        }
        return $selected;
    }
}