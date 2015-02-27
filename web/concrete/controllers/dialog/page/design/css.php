<?php
namespace Concrete\Controller\Dialog\Page\Design;
use \Concrete\Core\StyleCustomizer\CustomCssRecord;
use Core;

class Css extends \Concrete\Controller\Backend\UserInterface\Page {

    protected $viewPath = '/dialogs/page/design/css';
    public function view()
    {
        $this->requireAsset('ace');
        $sccRecordID = 0;
        $value = '';

        if ($this->request->query->has('sccRecordID')) {
            $sccRecord = CustomCssRecord::getByID($this->request->query->get('sccRecordID'));
            if (is_object($sccRecord)) {
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

            Core::make('helper/ajax')->sendResult($ax);
        }
    }

}