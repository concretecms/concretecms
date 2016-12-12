<?php
namespace Concrete\Controller\Element\Dashboard\Reports\Forms;

use Concrete\Core\Controller\ElementController;

class Header extends ElementController
{

    public function getElement()
    {
        return 'dashboard/reports/forms/header';
    }

    public function view()
    {
        $db = \Database::connection();
        $this->set('supportsLegacy', $db->tableExists('btFormQuestions'));
    }

}
