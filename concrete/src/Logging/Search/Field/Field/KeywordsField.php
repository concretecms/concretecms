<?php

namespace Concrete\Core\Logging\Search\Field\Field;

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Logging\LogList;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;

class KeywordsField extends AbstractField
{
    protected $requestVariables = [
        'keywords'
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
     * @param LogList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterByKeywords($this->getData('keywords'));
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        return $form->text('keywords' , $this->getData('keywords'));
    }
}
