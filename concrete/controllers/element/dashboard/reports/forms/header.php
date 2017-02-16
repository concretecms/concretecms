<?php
namespace Concrete\Controller\Element\Dashboard\Reports\Forms;

use Concrete\Core\Controller\ElementController;

class Header extends ElementController
{
    protected $nodeId;

    /**
     * Header constructor.
     *
     * @param $nodeId
     */
    public function __construct($nodeId)
    {
        parent::__construct();
        $this->nodeId = $nodeId;
    }

    public function getElement()
    {
        return 'dashboard/reports/forms/header';
    }

    public function view()
    {
        $db = \Database::connection();
        $this->set('supportsLegacy', $db->tableExists('btFormQuestions'));

        if ($this->nodeId) {
            $this->set('exportURL', \URL::to('/dashboard/express/entries', 'csv_export', $this->nodeId));
        }
    }
}
