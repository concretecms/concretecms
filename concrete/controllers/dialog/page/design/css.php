<?php
namespace Concrete\Controller\Dialog\Page\Design;

use Concrete\Core\Entity\StyleCustomizer\CustomCssRecord;
use Concrete\Core\StyleCustomizer\CustomCssRecord as CustomCssRecordService;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Error\UserMessageException;

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

    public function getCss()
    {
        $this->validationToken = 'ccm-style-customizer-customcss-load';
        if (!$this->validateAction()) {
            throw new UserMessageException($this->error->toText());
        }
        $sccRecord = $this->app->make(CustomCssRecordService::class)->getByID((int) $this->request->query->get('sccRecordID'));
        $css = $sccRecord === null ? '' : (string) $sccRecord->getValue();

        return $this->app->make(ResponseFactoryInterface::class)->json(['css' => $css]);
    }

    public function setCss()
    {
        $this->validationToken = 'ccm-style-customizer-customcss-save';
        if (!$this->validateAction()) {
            throw new UserMessageException($this->error->toText());
        }
        $css = $this->request->request->get('value');
        if (!is_string($css)) {
            throw new UserMessageException('Invalid custom CSS value received');
        }
        $record = new CustomCssRecord();
        $record->setValue($css);
        $record->save();

        return $this->app->make(ResponseFactoryInterface::class)->json(['sccRecordID' => (int) $record->getRecordID()]);
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
