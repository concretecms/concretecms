<?php
namespace Concrete\Core\Page\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\File\Type\Type;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ItemList\ItemList;

class PageTemplateField extends AbstractField
{

    protected $requestVariables = [
        'pTemplateID'
    ];

    public function getKey()
    {
        return 'page_template';
    }

    public function getDisplayName()
    {
        return t('Page Template');
    }

    /**
     * @param FileList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $template = \PageTemplate::getByID($this->data['pTemplateID']);
        if (is_object($template)) {
            $list->filterByPageTemplate($template);
        }
    }

    public function renderSearchField()
    {
        $form = \Core::make("helper/form");
        $html = $form->select('pTemplateID', array_reduce(
            \PageTemplate::getList(), function ($templates, $template) {
            $templates[$template->getPageTemplateID()] = $template->getPageTemplateDisplayName();
            return $templates;
        }
        ), $this->data['pTemplateID']);
        return $html;
    }


}
