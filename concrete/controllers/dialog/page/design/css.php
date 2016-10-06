<?php
namespace Concrete\Controller\Dialog\Page\Design;

use Concrete\Core\Entity\StyleCustomizer\CustomCssRecord;
use Concrete\Core\StyleCustomizer\CustomCssRecord as CustomCssRecordService;
use Concrete\Core\Http\ResponseFactoryInterface;

class Css extends \Concrete\Controller\Backend\UserInterface\Page
{
    protected $viewPath = '/dialogs/page/design/css';

    public function view()
    {
        $this->requireAsset('ace');
        $sccRecordID = 0;
        $value = '';

        if ($this->request->query->has('sccRecordID')) {
            $sccRecord = $this->app->make(CustomCssRecordService::class)->getByID((int) $this->request->query->get('sccRecordID'));
            if ($sccRecord !== null) {
                $value = $sccRecord->getValue();
                $sccRecordID = $sccRecord->getRecordID();
            }
        }
        $this->set('value', $value);
        $this->set('sccRecordID', $sccRecordID);
    }

    public function canAccess()
    {
        return $this->permissions->canEditPageTheme();
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $record = new CustomCssRecord();
            $record->setValue($this->request->request('value'));
            $record->save();

            $ax = new \stdClass();
            $ax->sccRecordID = $record->getRecordID();

            return $this->app->make(ResponseFactoryInterface::class)->json($ax);
        }
    }
}
