<?php

namespace Concrete\Core\User\Group\Search\Field\Field;

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\Group\GroupList;

class NameField extends AbstractField
{

    protected $requestVariables = [
        'gName',
    ];

    public function getKey()
    {
        return 'name';
    }

    public function getDisplayName()
    {
        return t('Name');
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        $html = $form->text('gName', $this->getData("gName"));
        return $html;

    }/** @noinspection PhpDocSignatureInspection */

    /**
     * @param GroupList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $list->filterByName($this->data['gName']);
    }

}
