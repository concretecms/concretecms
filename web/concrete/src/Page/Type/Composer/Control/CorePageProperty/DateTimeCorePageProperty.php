<?php
namespace Concrete\Core\Page\Type\Composer\Control\CorePageProperty;

use Loader;
use Page;

class DateTimeCorePageProperty extends CorePageProperty
{
    public function __construct()
    {
        $this->setCorePagePropertyHandle('date_time');
        $this->setPageTypeComposerControlName(tc('PageTypeComposerControlName', 'Public Date/Time'));
        $this->setPageTypeComposerControlIconSRC(ASSETS_URL . '/attributes/date_time/icon.png');
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
            $e->add(t('You must specify a valid date/time for this page.'));

            return $e;
        }
    }

    public function getPageTypeComposerControlDraftValue()
    {
        $c = $this->page;

        return $c->getCollectionDatePublic();
    }

}
