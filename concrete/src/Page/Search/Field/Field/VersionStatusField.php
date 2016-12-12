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
        if (isset($this->data['versionToRetrieve']) && $this->data['versionToRetrieve']) {
            $versionToRetrieve = $this->data['versionToRetrieve'];
        } else {
            $versionToRetrieve = PageList::PAGE_VERSION_ACTIVE;
        }
        $list->setPageVersionToRetrieve($versionToRetrieve);
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();

        if (isset($this->data['versionToRetrieve']) && $this->data['versionToRetrieve']) {
            $versionToRetrieve = $this->data['versionToRetrieve'];
        } else {
            $versionToRetrieve = PageList::PAGE_VERSION_RECENT;
        }

        $form = $app->make('helper/form');
        $html = '<div class="radio"><label>' . $form->radio('versionToRetrieve', PageList::PAGE_VERSION_RECENT, $versionToRetrieve) . t('All') . '</label></div>';
        $html .= '<div class="radio"><label>' . $form->radio('versionToRetrieve', PageList::PAGE_VERSION_ACTIVE, $versionToRetrieve) . t('Approved') . '</label></div>';
        $html .= '<div class="radio"><label>' . $form->radio('versionToRetrieve', PageList::PAGE_VERSION_RECENT_UNAPPROVED, $versionToRetrieve) . t('Unapproved') . '</label></div>';

        return $html;
    }
}
