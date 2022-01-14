<?php

namespace Concrete\Core\Logging\Search\Field\Field;

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Logging\Levels;
use Concrete\Core\Logging\LogList;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Monolog\Logger as Monolog;

class LevelField extends AbstractField
{
    protected $requestVariables = [
        'level'
    ];

    public function getKey()
    {
        return 'level';
    }

    public function getDisplayName()
    {
        return t('Level');
    }

    /**
     * @param LogList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filter('level', $this->getData('level'));
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        $levels = [];
        foreach (Monolog::getLevels() as $level) {
            $levels[$level] = Levels::getLevelDisplayName($level);
        }
        return $form->selectMultiple('level[]', $levels, $this->getData('level'));
    }
}

