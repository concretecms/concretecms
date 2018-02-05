<?php
namespace Concrete\Controller\Element\Dashboard\Reports\Forms;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Express\Entity;

class Header extends ElementController
{
    protected $nodeId;
    protected $entity;

    /**
     * Header constructor.
     *
     * @param $nodeId
     */
    public function __construct($nodeId, Entity $entity = null)
    {
        parent::__construct();
        $this->entity = $entity;
        $this->nodeId = $nodeId;
    }

    public function getElement()
    {
        return 'dashboard/reports/forms/header';
    }

    public function view()
    {
        $db = \Database::connection();
        if ($this->entity) {
            $this->set('entity', $this->entity);
            $this->set('exportURL', \URL::to('/dashboard/reports/forms', 'csv_export', $this->nodeId));
            $managePage = \Page::getByPath('/dashboard/system/express/entities');
            $permissions = new \Permissions($managePage);
            if ($permissions->canViewPage()) {
                $this->set('manageURL', \URL::to('/dashboard/system/express/entities', 'view_entity', $this->entity->getID()));
            }

        } else {
            $this->set('supportsLegacy', $db->tableExists('btFormQuestions'));
        }
    }
}
