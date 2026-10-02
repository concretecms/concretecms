<?php
namespace Concrete\Core\Application\UserInterface\Menu\Item;

use Concrete\Core\Asset\CssAsset;
use Concrete\Core\Asset\JavascriptAsset;
use Concrete\Core\Controller\AbstractController;
use HtmlObject\Element;
use HtmlObject\Link;

class Controller extends AbstractController implements ControllerInterface
{
    /** @var \Concrete\Core\Application\UserInterface\Menu\Item\Item */
    protected $menuItem;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Application\UserInterface\Menu\Item\ItemInterface|null $item if NULL, you have to call `setMenuItem()` later on.
     */
    public function __construct(?ItemInterface $item = null)
    {
        parent::__construct();
        if ($item !== null) {
            $this->setMenuItem($item);
        }
    }

    public function displayItem()
    {
        return true;
    }

    /**
     * @return \HtmlObject\Traits\Tag
     */
    public function getMenuItemLinkElement()
    {
        $a = new Link();
        $a->setValue('');
        $icon_str = $this->menuItem->getIcon();
        if ($icon_str) {
            $icon = new Element('i');
            /*
             * Allows menu items to set icons with full FA spec such as fas,far...fab
             * Defaults to prior behaviour of prefixing if full FA spec is not specified
             */
            if(preg_match("/\b(fa|fas|far|fal|fad|fab)\b/",$icon_str)){
                $icon->addClass($icon_str);
            } else {
                $icon->addClass('fa fa-' . $icon_str);
            }
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
                        $asset = new CssAsset($identifier);
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
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Application\UserInterface\Menu\Item\ControllerInterface::getMenuItem()
     *
     * @return \Concrete\Core\Application\UserInterface\Menu\Item\Item
     */
    public function getMenuItem()
    {
        return $this->menuItem;
    }

    public function setMenuItem(ItemInterface $obj)
    {
        if (!$obj instanceof Item) {
            throw new \InvalidArgumentException(t('The menu item must be an instance of %s.', Item::class));
        }
        $this->menuItem = $obj;
    }
}
