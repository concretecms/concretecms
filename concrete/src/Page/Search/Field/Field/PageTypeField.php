<?php
namespace Concrete\Core\Page\Search\Field\Field;

use Concrete\Core\Backup\ContentExporter;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Type\Type;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ItemList\ItemList;

class PageTypeField extends AbstractField
{

    protected $requestVariables = [
        'ptID'
    ];

    public function getKey()
    {
        return 'page_type';
    }

    public function getDisplayName()
    {
        return t('Page Type');
    }

    /**
     * @param FileList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $list->filterByPageTypeID($this->getData('ptID'));
    }

    public function renderSearchField()
    {
        $html = '';
        $types = \Core::make('site/type')->getList();
        if (count($types) == 1) {
            $form = \Core::make('helper/form');
            $html = $form->select('ptID', array_reduce(
                \PageType::getList(), function ($types, $type) {
                $types[$type->getPageTypeID()] = $type->getPageTypeDisplayName();
                return $types;
            }
            ), $this->getData('ptID'));
        } else {
            $html = '<select name="ptID" class="form-select">';
            foreach($types as $type) {
                $html .= '<optgroup label="' . $type->getSiteTypeName() . '">';
                $types = \PageType::getList(false, $type);
                foreach($types as $pageType) {
                    $selected = $pageType->getPageTypeID() == $this->getData('ptID') ? ' selected ' : '';
                    $html .= '<option value="' . $pageType->getPageTypeID() . '" ' . $selected . '>' . $pageType->getPageTypeDisplayName() . '</option>';
                }
                $html .= '</optgroup>';
            }
            $html .= '</select>';
        }
        return $html;
    }

    public function export(\SimpleXMLElement $element)
    {
        $fieldNode = $element->addChild('field');
        $fieldNode->addAttribute('key', $this->getKey());
        $fieldNode->addChild('pagetype', ContentExporter::replacePageTypeWithPlaceHolder($this->getData('ptID')));
    }

    public function loadDataFromImport(\SimpleXMLElement $element)
    {
        if (!$this->isLoaded) {
            $inspector = app('import/value_inspector');
            $result = $inspector->inspect((string) $element->pagetype);
            $this->data['ptID'] = $result->getReplacedValue();
            $this->isLoaded = true;
        }
    }


}
