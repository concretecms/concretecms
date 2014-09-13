<?php
namespace Concrete\Core\Page\Type\Composer\Control\CorePageProperty;

use Loader;
use Page;

class UrlSlugCorePageProperty extends CorePageProperty
{
    public function __construct()
    {
        $this->setCorePagePropertyHandle('url_slug');
        $this->setPageTypeComposerControlName(tc('PageTypeComposerControlName', 'URL Slug'));
        $this->setPageTypeComposerControlIconSRC(ASSETS_URL . '/attributes/text/icon.png');
    }

    public function publishToPage(Page $c, $data, $controls)
    {
        $this->addPageTypeComposerControlRequestValue('cHandle', $data['url_slug']);
        parent::publishToPage($c, $data, $controls);
    }

    public function validate()
    {
        $e = Loader::helper('validation/error');
        $handle = $this->getPageTypeComposerControlDraftValue();
        if (!$handle) {
            $e->add(t('You must specify a URL slug.'));

            return $e;
        }
    }

    public function getPageTypeComposerControlDraftValue()
    {
        if (is_object($this->page)) {
            $c = $this->page;

            return $c->getCollectionHandle();
        }
    }

}
