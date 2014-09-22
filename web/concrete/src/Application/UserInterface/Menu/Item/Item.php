<?php
namespace Concrete\Core\Application\UserInterface\Menu\Item;
use Core;
use Concrete\Core\Asset\CssAsset;
use Concrete\Core\Asset\JavascriptAsset;

class Item
{

    public function __construct($handle, $pkgHandle = false)
    {
        $this->handle = $handle;
        $this->pkgHandle = $pkgHandle;
        $al = \AssetList::getInstance();
        $v = \View::getInstance();
        $env = \Environment::get();
        $identifier = 'menuitem/' . $this->handle . '/view';
        foreach(array('CSS' => 'view.css', 'JAVASCRIPT' => 'view.js') as $t => $i) {
            $r = $env->getRecord(DIRNAME_MENU_ITEMS . '/' . $handle . '/' . $i, $pkgHandle);
            if ($r->exists()) {
                switch($t) {
                    case 'CSS':
                        $asset = new CSSAsset($identifier);
                        $asset->setAssetURL($r->url);
                        $asset->setAssetPath($r->file);
                        $al->registerAsset($asset);
                        $v->requireAsset('css', $identifier);
                        break;
                    case 'JAVASCRIPT':
                        $asset = new JavascriptAsset($identifier);
                        $asset->setAssetURL($r->url);
                        $asset->setAssetPath($r->file);
                        $al->registerAsset($asset);
                        $v->requireAsset('javascript', $identifier);
                        break;
                }
            }
        }
    }

    protected $controller;

    public function getHandle()
    {
        return $this->handle;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function getLinkAttributes()
    {
        return $this->linkAttributes;
    }

    public function setLinkAttributes($linkAttributes)
    {
        $this->linkAttributes = $linkAttributes;
    }

    public function setLink($href)
    {
        $this->href = $href;
    }

    public function getLink()
    {
        return $this->href;
    }

    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getPackageObject()
    {
        return $this->pkgHandle;
    }

    public function getController()
    {
        if (isset($this->controller)) {
            return $this->controller;
        } else {
            $class = overrideable_core_class(
                'MenuItem\\' . camelcase($this->handle) . '\\Controller',
                DIRNAME_MENU_ITEMS . '/' . $this->handle . '/' . FILENAME_CONTROLLER,
                $this->pkgHandle
            );
            $this->controller = \Core::make($class, array($this));
            $this->controller->setMenuItem($this);
            return $this->controller;
        }
    }

}
