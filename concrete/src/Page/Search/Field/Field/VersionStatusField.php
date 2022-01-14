<?php
namespace Concrete\Core\Page\Search\Field\Field;

use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Page\PageList;
use Concrete\Core\Support\Facade\Application;

class VersionStatusField extends AbstractField
{
    protected $requestVariables = [
        'versionToRetrieve',
    ];

    public function getKey()
    {
        return 'version_status';
    }

    public function getDisplayName()
    {
        return t('Version Status');
    }

    /**
     * @param ItemList $list
     */
    public function filterList(ItemList $list)
    {
        if (isset($this->data['versionToRetrieve']) && $this->getData('versionToRetrieve')) {
            $versionToRetrieve = $this->getData('versionToRetrieve');
        } else {
            $versionToRetrieve = PageList::PAGE_VERSION_ACTIVE;
        }
        $list->setPageVersionToRetrieve($versionToRetrieve);
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();

        if (isset($this->data['versionToRetrieve']) && $this->getData('versionToRetrieve')) {
            $versionToRetrieve = $this->getData('versionToRetrieve');
        } else {
            $versionToRetrieve = PageList::PAGE_VERSION_RECENT;
        }

        $form = $app->make('helper/form');
        $html = '<div class="form-check">' . $form->radio('versionToRetrieve', PageList::PAGE_VERSION_RECENT, $versionToRetrieve) . $form->label('versionToRetrieve' . '1',t('All'), ['class'=>'form-check-label']) . '</div>';
        $html .= '<div class="form-check">' . $form->radio('versionToRetrieve', PageList::PAGE_VERSION_ACTIVE, $versionToRetrieve) . $form->label('versionToRetrieve' . '2',t('Approved'), ['class'=>'form-check-label']) . '</div>';
        $html .= '<div class="form-check">' . $form->radio('versionToRetrieve', PageList::PAGE_VERSION_SCHEDULED, $versionToRetrieve) . $form->label('versionToRetrieve' . '3',t('Scheduled'), ['class'=>'form-check-label']) . '</div>';
        $html .= '<div class="form-check">' . $form->radio('versionToRetrieve', PageList::PAGE_VERSION_RECENT_UNAPPROVED, $versionToRetrieve) . $form->label('versionToRetrieve' . '4',t('Unapproved'), ['class'=>'form-check-label']) . '</div>';

        return $html;
    }
}
