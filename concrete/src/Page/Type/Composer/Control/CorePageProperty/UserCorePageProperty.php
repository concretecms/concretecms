<?php

namespace Concrete\Core\Page\Type\Composer\Control\CorePageProperty;

use Core;
use UserInfo;
use Concrete\Core\Page\Page;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;

class UserCorePageProperty extends CorePageProperty
{
    public function __construct()
    {
        $this->setCorePagePropertyHandle('user');
        $this->setPageTypeComposerControlIconFormatter(new FontAwesomeIconFormatter('file-text'));
    }

    public function getPageTypeComposerControlName()
    {
        return tc('PageTypeComposerControlName', 'User');
    }

    public function publishToPage(Page $c, $data, $controls)
    {
        if (!is_array($data)) {
            $data = [];
        }
        $data += [
            'user' => null,
        ];
        if (Core::make('helper/validation/numbers')->integer($data['user'])) {
            $this->addPageTypeComposerControlRequestValue('uID', $data['user']);
        }
        parent::publishToPage($c, $data, $controls);
    }

    public function validate()
    {
        $uID = $this->getPageTypeComposerControlDraftValue();
        $ux = UserInfo::getByID($uID);
        $e = Core::make('helper/validation/error');
        if (!is_object($ux)) {
            $control = $this->getPageTypeComposerFormLayoutSetControlObject();
            $e->add(t('You haven\'t chosen a valid %s', $control->getPageTypeComposerControlDisplayLabel()));

            return $e;
        }
    }

    public function getPageTypeComposerControlDraftValue()
    {
        if (is_object($this->page)) {
            $c = $this->page;

            return $c->getCollectionUserID();
        }
    }
}
