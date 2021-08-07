<?php
namespace Concrete\Core\View;

use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Page\Page;

/**
 * Class PreviewView
 * Used when custom component previews in the Dashboard show a page + colors, typography, etc...
 */
class PreviewView extends View
{

    /**
     * @var Page
     */
    protected $proxyPage;

    /**
     * @var Element
     */
    protected $previewElement;

    public function constructView($path = false)
    {
        $locator = app(FileLocator::class);
        $this->setInnerContentFile($locator->getRecord(DIRNAME_VIEWS . DIRECTORY_SEPARATOR . $path . '.php')->getFile());
    }

    /**
     * @return Page
     */
    public function getProxyPage(): Page
    {
        return $this->proxyPage;
    }

    /**
     * @param Page $proxyPage
     */
    public function setProxyPage(Page $proxyPage): void
    {
        $this->proxyPage = $proxyPage;
    }

    /**
     * @return Element
     */
    public function getPreviewElement(): Element
    {
        return $this->previewElement;
    }

    /**
     * @param Element $previewElement
     */
    public function setPreviewElement(Element $previewElement): void
    {
        $this->previewElement = $previewElement;
    }

    public function getScopeItems()
    {
        $items = parent::getScopeItems();
        $items['c'] = $this->getProxyPage();
        $items['theme'] = $this->themeObject;
        $items['previewElement'] = $this->getPreviewElement();
        return $items;
    }


}
