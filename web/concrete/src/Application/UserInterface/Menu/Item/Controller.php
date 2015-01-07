<?php
namespace Concrete\Core\Application\UserInterface\Menu\Item;

use Concrete\Core\Asset\CssAsset;
use Concrete\Core\Asset\JavascriptAsset;
use Concrete\Core\Controller\AbstractController;
use HtmlObject\Element;
use HtmlObject\Link;

class Controller extends AbstractController implements ControllerInterface
{

    /** @var ItemInterface */
    protected $menuItem;

    public function displayItem()
    {
        return true;
    }

    public function getMenuItemLinkElement()
    {
        $a = new Link();
        $a->setValue('');
        if ($this->menuItem->getIcon()) {
            $icon = new Element('i');
            $icon->addClass('fa fa-' . $this->menuItem->getIcon());
            $a->appendChild($icon);
        }

        if ($this->menuItem->getLink()) {
            $a->href($this->menuItem->getLink());
        }

        foreach ($this->menuItem->getLinkAttributes() as $key => $value) {
            $a->setAttribute($key, $value);
        }

        $label = new Element('span');
        $label->addClass('ccm-toolbar-accessibility-title')->setValue($this->menuItem->getLabel());
        $a->appendChild($label);
        return $a;
    }

    public function registerViewAssets()
    {
        $al = \AssetList::getInstance();
        $v = \View::getInstance();
        $env = \Environment::get();
        $identifier = 'menuitem/' . $this->menuItem->getHandle() . '/view';
        foreach (array('CSS' => 'view.css', 'JAVASCRIPT' => 'view.js') as $t => $i) {
            $r = $env->getRecord(
                DIRNAME_MENU_ITEMS . '/' . $this->menuItem->getHandle() . '/' . $i,
                $this->menuItem->getPackageHandle());
            if ($r->exists()) {
                switch ($t) {
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

    /**
     * @return ItemInterface
     */
    public function getMenuItem()
    {
        return $this->menuItem;
    }

    public function setMenuItem(ItemInterface $obj)
    {
        $this->menuItem = $obj;
    }
}
