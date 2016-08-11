<?php
namespace Concrete\Core\Page\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\File\Type\Type;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ItemList\ItemList;

class VersionStatusField extends AbstractField
{

    protected $requestVariables = [
        'versionToRetrieve'
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
     * @param FileList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $list->setPageVersionToRetrieve($this->data['versionToRetrieve']);
    }

    public function renderSearchField()
    {
        $versionToRetrieve = \Concrete\Core\Page\PageList::PAGE_VERSION_RECENT;
        if ($this->data['versionToRetrieve']) {
            $versionToRetrieve = $this->data['versionToRetrieve'];
        }
        $form = \Core::make('helper/form');
        $html = '<div class="radio"><label>' . $form->radio('versionToRetrieve', \Concrete\Core\Page\PageList::PAGE_VERSION_RECENT, $versionToRetrieve) . t('All') . '</label></div>';
        $html .= '<div class="radio"><label>' . $form->radio('versionToRetrieve', \Concrete\Core\Page\PageList::PAGE_VERSION_ACTIVE, $versionToRetrieve) . t('Approved') . '</label></div>';
        return $html;
    }


}
