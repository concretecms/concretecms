<?php
namespace Concrete\Core\Workflow;

use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Package\PackageList;

class Type extends ConcreteObject
{
    /**
     * Returns this Workflow Type's ID
     *
     * @return int
     */
    public function getWorkflowTypeID()
    {
        return $this->wftID;
    }

    /**
     * Returns this Workflow Type's Handle
     *
     * @return string
     */
    public function getWorkflowTypeHandle()
    {
        return $this->wftHandle;
    }
    /**
     * Returns this Workflow Type's Name
     *
     * @return string
     */
    public function getWorkflowTypeName()
    {
        return $this->wftName;
    }

    /**
     * Gets a WorkflowType By ID
     *
     * @param $wftID int
     * @return Type | null
     */
    public static function getByID($wftID)
    {
        $app = Application::getFacadeApplication();
        /** @var $db \Concrete\Core\Database\Connection\Connection */
        $db = $app->make('database')->connection();
        $qb = $db->createQueryBuilder();
        $qb->select('wftID, pkgID, wftHandle, wftName')
            ->from('WorkflowTypes')
            ->where($qb->expr()->eq('wftID', $wftID))->setMaxResults(1);

        $row = $qb->execute()->fetch();
        if ($row['wftHandle']) {
            $wt = new static();
            $wt->setPropertiesFromArray($row);

            return $wt;
        }

        return null;
    }

    /**
     * Returns a list of WorkflowTypes currently installed
     *
     * @return Type[]
     */
    public static function getList()
    {
        $app = Application::getFacadeApplication();
        /** @var $db \Concrete\Core\Database\Connection\Connection */
        $db = $app->make('database')->connection();
        $list = [];
        $qb = $db->createQueryBuilder();
        $qb->select('wftID')->from('WorkflowTypes')->orderBy('wftID', 'asc');
        $result = $qb->execute()->fetchColumn();

        foreach ($result as $id) {
            $list[] = static::getByID($id);
        }


        return $list;
    }

    /**
     * This function is used to add the workflow type list to an existing XML export file.
     *
     * @param \SimpleXMLElement $xml
     */
    public static function exportList($xml)
    {
        $wtypes = static::getList();
        $axml = $xml->addChild('workflowtypes');
        foreach ($wtypes as $wt) {
            $wtype = $axml->addChild('workflowtype');
            $wtype->addAttribute('handle', $wt->getWorkflowTypeHandle());
            $wtype->addAttribute('name', $wt->getWorkflowTypeName());
            $wtype->addAttribute('package', $wt->getPackageHandle());
        }
    }

    /**
     * Gets all workflows belonging to this type.
     *
     * @return Workflow[]
     */
    public function getWorkflows() {
        $workflows = [];
        $app = Application::getFacadeApplication();
        /** @var $db \Concrete\Core\Database\Connection\Connection */
        $db = $app->make('database')->connection();
        $qb = $db->createQueryBuilder();
        $qb->select('wfID')->from('Workflows')
            ->where($qb->expr()->eq('wftID',$this->getWorkflowTypeID()));
        $results = $qb->execute()->fetchColumn();
        foreach ($results as $result) {
            $workflow = Workflow::getByID($result);
            if (is_object($workflow)) {
                $workflows[] = $workflow;
            }
        }

        return $workflows;

    }

    /**
     * Deletes this workflow type and all workflows belonging to this type.
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function delete()
    {
        $workflows = $this->getWorkflows();
        foreach ($workflows as $workflow) {
            $workflow->delete();
        }
        $app = Application::getFacadeApplication();
        /* @var $db \Concrete\Core\Database\Connection\Connection */
        $db = $app->make('database')->connection();
        $db->delete('WorkflowTypes',['wftID', $this->getWorkflowTypeID()]);
    }

    /**
     * @param $pkg \Concrete\Core\Package\Package | \Concrete\Core\Entity\Package
     * @return Type[]
     */
    public static function getListByPackage($pkg)
    {
        $list = [];
        $app = Application::getFacadeApplication();
        /** @var $db \Concrete\Core\Database\Connection\Connection */
        $db = $app->make('database')->connection();
        $qb = $db->createQueryBuilder();
        $qb->select('wfID')->from('WorkflowTypes')
            ->where($qb->expr('pkgID'), $pkg->getPackageID());
        $result = $qb->execute()->fetchColumn();

        foreach ($result as $id) {
            $list[] = static::getByID($id);
        }


        return $list;
    }

    /**
     * Returns this Workflow Type's Package ID.
     *
     * @return int
     */
    public function getPackageID()
    {
        return (int) $this->pkgID;
    }

    /**
     * Returns this Workflow Type's Package handle
     *
     * Will return false, if no package.
     *
     * @return string|bool
     */
    public function getPackageHandle()
    {
        return PackageList::getHandle($this->pkgID);
    }

    /**
     * Gets a Workflow Type by handle
     *
     * @param $wftHandle
     * @return Type|null
     */
    public static function getByHandle($wftHandle)
    {
        $app = Application::getFacadeApplication();
        /** @var $db \Concrete\Core\Database\Connection\Connection */
        $db = $app->make('database')->connection();
        $qb = $db->createQueryBuilder();
        $qb->select('wfID')->from('WorkflowTypes')
            ->where($qb->expr('wftHandle'), $wftHandle)->setMaxResults(1);
        $wftID = $qb->execute()->fetchColumn();
        if ($wftID > 0) {
            return self::getByID($wftID);
        }
    }

    /**
     * Add a new Workflow Type.
     *
     * e.g Type::add('wft_handle','Type Name') - for no package
     * e.g Type::add('wft_handle','Type Name', $pkg) - with package
     * where $pkg is a Package Object/Entity
     *
     * @param $wftHandle string
     * @param $wftName string
     * @param \Concrete\Core\Package\Package | \Concrete\Core\Entity\Package | bool $pkg
     * @return Type|null
     */
    public static function add($wftHandle, $wftName, $pkg = false)
    {
        $pkgID = 0;
        if (is_object($pkg)) {
            $pkgID = $pkg->getPackageID();
        }
        $app = Application::getFacadeApplication();
        /** @var $db \Concrete\Core\Database\Connection\Connection */
        $db = $app->make('database')->connection();
        $db->insert('WorkflowTypes',
            ['wftHandle'=>$wftHandle, 'wftName'=>$wftName, 'pkgID', $pkgID],
            [\PDO::STR, \PDO::STR, \PDO::INT]);
        $id = $db->lastInsertId();
        $est = static::getByID($id);

        return $est;
    }
}
