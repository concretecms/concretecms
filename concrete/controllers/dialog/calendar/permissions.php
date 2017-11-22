<?php
namespace Concrete\Controller\Dialog\Calendar;

use Concrete\Core\View\DialogView;
use Concrete\Core\Calendar\Calendar;

class Permissions extends \Concrete\Core\Controller\Controller
{
    public function view($pkCategoryHandle)
    {
        if ($this->request->query->has('caID')) {
            $calendar = Calendar::getByID($this->request->query->get('caID'));
            $this->set('calendar', $calendar);
        }
        $v = new DialogView('/dialogs/permissions/' . $pkCategoryHandle);
        $v->setController($this);
        return $v;
    }
}
