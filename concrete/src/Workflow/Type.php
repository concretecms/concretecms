<?php

namespace Concrete\Core\Workflow;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Support\Facade\Application;
use PDO;
use Punic\Comparer;

class Type extends ConcreteObject
{
    /**
     * Get the ID of this workflow type.
     *
     * @return int
     */
    public function getWorkflowTypeID()
    {
        return $this->wftID;
    }

    /**
     * Get the handle of this workflow type.
     *
     * @return string
     */
    public function getWorkflowTypeHandle()
    {
        return $this->wftHandle;
    }

    /**
     * Get the name of this workflow type.
     *
     * @return string
     */
    public function getWorkflowTypeName()
    {
        return $this->wftName;
    }

    /**
     * Get the ID of the package that created this workflow type.
     *
     * @return int zero if no package defined this workflow type, the package ID otherwise
     */
    public function getPackageID()
    {
        return $this->pkgID;
    }

    /**
     * Get the handle of the package that created this workflow type.
     *
     * @return string|bool false if no package defined this workflow type, the package handle otherwise
     */
    public function getPackageHandle()
    {
        $pkgID = $this->getPackageID();

        return $pkgID ? PackageList::getHandle($pkgID) : false;
    }

    /**
     * Gets all workflows belonging to this type, sorted by their display name.
     *
     * @return \Concrete\Core\Workflow\Workflow[]
     */
    public function getWorkflows()
    {
        $workflows = [];
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $qb = $db->createQueryBuilder();
        $qb
            ->select('wfID')
            ->from('Workflows')
            ->where($qb->expr()->eq('wftID', $this->getWorkflowTypeID()));
        $rows = $qb->execute()->fetchAll();
        foreach ($rows as $row) {
            $workflow = Workflow::getByID($row['wfID']);
            if ($workflow !== null) {
                $workflows[] = $workflow;
            }
        }
        $cmp = new Comparer();
        usort($workflows, function (Workflow $a, Workflow $b) use ($cmp) {
            return $cmp->compare($a->getWorkflowDisplayName('text'), $b->getWorkflowDisplayName('text'));
        });

        return $workflows;
    }

    /**
     * Delete this workflow type and all workflows belonging to this type.
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
        $db = $app->make(Connection::class);
        $db->delete('WorkflowTypes', ['wftID' => $this->getWorkflowTypeID()]);
    }

    /**
     * Add a new workflow type.
     *
     * @param string $wftHandle The handle of the new workflow type
     * @param string $wftName The name of the new workflow type
     * @param \Concrete\Core\Package\Package|\Concrete\Core\Entity\Package|null|bool $pkg the package that's creating the new workflow type
     *
     * @return \Concrete\Core\Workflow\Type
     */
    public static function add($wftHandle, $wftName, $pkg = false)
    {
        $pkgID = is_object($pkg) ? (int) $pkg->getPackageID() : 0;
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->insert(
            'WorkflowTypes',
            [
                'wftHandle' => $wftHandle,
                'wftName' => $wftName,
                'pkgID' => $pkgID,
            ],
            [
                PDO::PARAM_STR,
                PDO::PARAM_STR,
                PDO::PARAM_INT,
            ]
        );
        $id = $db->lastInsertId();

        return static::getByID($id);
    }

    /**
     * Get a workflow type given its ID.
     *
     * @param int $wftID
     *
     * @return \Concrete\Core\Workflow\Type|null
     */
    public static function getByID($wftID)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $qb = $db->createQueryBuilder();
        $qb
            ->select('wftID, pkgID, wftHandle, wftName')
            ->from('WorkflowTypes')
            ->where($qb->expr()->eq('wftID', (int) $wftID))
            ->setMaxResults(1)
        ;
        $row = $qb->execute()->fetch();
        if (!$row) {
            return null;
        }
        $wt = new static();
        $row['wftID'] = (int) $row['wftID'];
        $row['pkgID'] = (int) $row['pkgID'];
        $wt->setPropertiesFromArray($row);

        return $wt;
    }

    /**
     * Gets a Workflow Type by handle.
     *
     * @param $wftHandle
     *
     * @return Type|null
     */
    public static function getByHandle($wftHandle)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $qb = $db->createQueryBuilder();
        $qb
            ->select('wftID')
            ->from('WorkflowTypes')
            ->where($qb->expr()->eq('wftHandle', $qb->createNamedParameter((string) $wftHandle)))
            ->setMaxResults(1);
        $wftID = $qb->execute()->fetchColumn();

        return $wftID ? self::getByID($wftID) : null;
    }

    /**
     * Get the list of the currently installed workflow types, sorted by their name.
     *
     * @return \Concrete\Core\Workflow\Type[]
     */
    public static function getList()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $qb = $db->createQueryBuilder();
        $qb
            ->select('wftID')
            ->from('WorkflowTypes')
            ->orderBy('wftID', 'asc')
        ;
        $list = [];
        foreach ($qb->execute()->fetchAll() as $row) {
            $list[] = static::getByID($row['wftID']);
        }
        $cmp = new Comparer();
        usort($list, function (Type $a, Type $b) use ($cmp) {
            return $cmp->compare($a->getWorkflowTypeName(), $b->getWorkflowTypeName());
        });

        return $list;
    }

    /**
     * Get the list of the currently installed workflow types that were created by a package, sorted by their name.
     *
     * @param \Concrete\Core\Package\Package|\Concrete\Core\Entity\Package $pkg
     *
     * @return \Concrete\Core\Workflow\Type[]
     */
    public static function getListByPackage($pkg)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $qb = $db->createQueryBuilder();
        $qb
            ->select('wftID')
            ->from('WorkflowTypes')
            ->where($qb->expr()->eq('pkgID', $pkg->getPackageID()))
        ;
        $list = [];
        foreach ($qb->execute()->fetchAll() as $row) {
            $list[] = static::getByID($row['wftID']);
        }
        $cmp = new Comparer();
        usort($list, function (Type $a, Type $b) use ($cmp) {
            return $cmp->compare($a->getWorkflowTypeName(), $b->getWorkflowTypeName());
        });

        return $list;
    }

    /**
     * Export the currently installed workflow types to XML.
     *
     * @param \SimpleXMLElement $xml
     */
    public static function exportList($xml)
    {
        $wtypes = static::getList();
        if (empty($wtypes)) {
            return;
        }
        $axml = $xml->addChild('workflowtypes');
        foreach ($wtypes as $wt) {
            $wtype = $axml->addChild('workflowtype');
            $wtype->addAttribute('handle', $wt->getWorkflowTypeHandle());
            $wtype->addAttribute('name', $wt->getWorkflowTypeName());
            $wtype->addAttribute('package', $wt->getPackageHandle() ?: '');
        }
    }
}
