<?php
namespace Concrete\Core\File\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ItemList\ItemList;

class AddedToPageField extends AbstractField
{

    protected $requestVariables = ['ocIDSearchField'];

    public function getKey()
    {
        return 'added_to_page';
    }

    public function getDisplayName()
    {
        return t('Added to Page');
    }

    public function renderSearchField()
    {
        $ps = \Core::make("helper/form/page_selector");
        return $ps->selectPage('ocIDSearchField', $this->data['ocIDSearchField']);
    }

    /**
     * @param FileList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $ocID = $this->data['ocIDSearchField'];
        if ($ocID > 0) {
            $list->filterByOriginalPageID($ocID);
        }
    }

}
