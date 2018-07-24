<?php
namespace Concrete\Core\Notification\Alert;

use Concrete\Core\Notification\Alert\Filter\FilterInterface;
use Concrete\Core\Search\ItemList\Database\ItemList;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\Pagination\PaginationProviderInterface;
use Concrete\Core\User\User;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Entity\Notification\NotificationAlert;

class AlertList extends ItemList implements PaginationProviderInterface
{
    protected $autoSortColumns = array('nDate');

    protected $user;
    protected $entityManager;

    public function __construct(EntityManager $em, User $user)
    {
        $this->user = $user;
        $this->entityManager = $em;
        parent::__construct();
    }

    public function createQuery()
    {
        $this->query->select('naID')
            ->from('NotificationAlerts', 'na')
            ->where('uID = :uID')
            ->leftJoin('na', 'Notifications', 'n', 'na.nID = n.nID')
            ->leftJoin('n', 'WorkflowProgressNotifications', 'wn', 'wn.nid = n.nID')
            ->leftJoin('wn', 'WorkflowProgress', 'wp', 'wn.wpID = wp.wpID');

        $this->query->setParameter('uID', $this->user->getUserID());
        $this->query->orderBy('nDate', 'desc');
    }

    public function getPaginationAdapter()
    {
        $adapter = new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->select('count(naID)')->setMaxResults(1);
        });
        return $adapter;
    }

    /**
     * The total results of the query.
     *
     * @return int
     */
    public function getTotalResults()
    {
        $query = $this->deliverQueryObject();
        return $query->select('count(naID)')->setMaxResults(1)->execute()->fetchColumn();
    }


    /**
     * @param $queryRow
     *
     * @return array
     */
    public function getResult($queryRow)
    {
        return $this->entityManager->find(NotificationAlert::class, $queryRow['naID']);
    }

}
