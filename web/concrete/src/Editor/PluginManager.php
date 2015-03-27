<?php
namespace Concrete\Core\Editor;

class PluginManager
{

    protected $plugins = array();
    protected $selectedPlugins = array();

    public function register($key, $name)
    {
        $this->plugins[$key] = $name;
    }

    public function getAvailablePlugins()
    {
        return $this->plugins;
    }

    public function isSelected($key)
    {
        return in_array($key, $this->selectedPlugins);
    }

    public function isAvailable($key)
    {
        return array_key_exists($key, $this->plugins);
    }

    public function select($key)
    {
        if ($this->isAvailable($key) && !$this->isSelected($key)) {
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
        return $this->selectedPlugins;
    }
}