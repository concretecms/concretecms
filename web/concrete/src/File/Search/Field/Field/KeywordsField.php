<?php
namespace Concrete\Core\File\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\File\Type\Type;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ItemList\ItemList;

class KeywordsField extends AbstractField
{


    public function __construct($keywords = null)
    {
        if ($keywords) {
            $this->data['keywords'] = $keywords;
        }
    }

    protected $requestVariables = [
        'keywords',
    ];

    public function getKey()
    {
        return 'keywords';
    }

    public function getDisplayName()
    {
        return t('Keywords');
    }

    /**
     * @param FileList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $keywords = $this->data['keywords'];
        $list->filterByKeywords($keywords);
    }

    public function renderSearchField()
    {
        $form = \Core::make('helper/form');
        return $form->text('keywords', $this->data['keywords']);
    }


}
