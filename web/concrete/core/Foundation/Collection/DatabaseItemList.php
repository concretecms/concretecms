<?
namespace Concrete\Core\Foundation\Collection;
use Database;
use Doctrine\DBAL\Logging\EchoSQLLogger;

abstract class DatabaseItemList implements ListItemInterface
{

    /** @var \Doctrine\DBAL\Query\QueryBuilder */
    protected $query;

    /** @var \Concrete\Core\Pagination\Pagination  */
    protected $pagination;

    public function __construct()
    {
        $this->query = Database::get()->createQueryBuilder();
        $this->createQuery();
    }

    /** Returns a full array of results. */
    public function getResults()
    {
        $results = array();
        foreach($this->query->execute()->fetchAll() as $result) {
            $r = $this->getResult($result);
            if ($r != null) {
                $results[] = $r;
            }
        }
        return $results;
    }

    public function sortBy($field, $directon = 'asc')
    {
        $this->query->orderBy($field, $directon);
    }

}