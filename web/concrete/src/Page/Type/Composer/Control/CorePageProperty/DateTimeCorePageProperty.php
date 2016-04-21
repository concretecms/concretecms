<?php
namespace Concrete\Core\Page\Type\Composer\Control\CorePageProperty;

use Loader;
use Page;

class DateTimeCorePageProperty extends CorePageProperty
{
    public function __construct()
    {
        $this->setCorePagePropertyHandle('date_time');
        $this->setPageTypeComposerControlIconSRC(ASSETS_URL . '/attributes/date_time/icon.png');
    }

    public function getPageTypeComposerControlName()
    {
        return tc('PageTypeComposerControlName', 'Public Date/Time');
    }

    public function publishToPage(Page $c, $data, $controls)
    {
        $this->addPageTypeComposerControlRequestValue('cDatePublic', Loader::helper('form/date_time')->translate('date_time', $data));
        parent::publishToPage($c, $data, $controls);
    }

    public function validate()
    {
        $e = Loader::helper('validation/error');
        $date = $this->getPageTypeComposerControlDraftValue();
        if (!strtotime($date)) {
            $control = $this->getPageTypeComposerFormLayoutSetControlObject();
            $e->add(t('You haven\'t chosen a valid %s', $control->getPageTypeComposerControlDisplayLabel()));

            return $e;
        }
    }

    public function getPageTypeComposerControlDraftValue()
    {
        if (is_object($this->page)) {
            $c = $this->page;
            return $c->getCollectionDatePublic();
        }
    }

}
