<?php 
namespace Concrete\Core\File;
use Database;
use Doctrine\DBAL\Logging\EchoSQLLogger;
use Doctrine\DBAL\Query;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta as Paginator;
use FileAttributeKey;

class FileList
{

    /** @var \Doctrine\ORM\EntityManager */
    protected $manager;

    /** @var \Pagerfanta\Pagerfanta */
    protected $paginator;

    /** @var \Doctrine\DBAL\Query\QueryBuilder */
    protected $query;

    /** @var integer */
    protected $maxPerPage = 10;

    /** @var integer */
    protected $currentPage = 1;

    public function getPaginator()
    {
        if (!isset($this->paginator)) {
            $adapter = new DoctrineDbalAdapter($this->query, function($query) {
               $query->select('count(distinct f.fID)')->setMaxResults(1);
            });
            $this->paginator = new Paginator($adapter);
            $this->paginator->setMaxPerPage($this->maxPerPage);
        }
        return $this->paginator;
    }

    public function __construct()
    {
        $this->query = Database::get()->createQueryBuilder();
        $this->query->select('f.fID')
            ->from('Files', 'f')
            ->innerJoin('f', 'FileVersions', 'fv', 'f.fID = fv.fID and fv.fvIsApproved = 1');
    }

    public function getTotal()
    {
        return $this->getPaginator()->getNbResults();
    }

    public function getPage()
    {
        $results = array();
        foreach($this->getPaginator()->getCurrentPageResults() as $fID) {
            $f = File::getByID($fID);
            if (is_object($f)) {
                $results[] = $f;
            }
        }
        return $results;
    }

    public function filterByType($type)
    {
        $this->query->andWhere('fv.fvType = :fvType');
        $this->query->setParameter('fvType', $type);
    }

    public function filterByExtension($extension)
    {
        $this->query->andWhere('fv.fvExtension = :fvExtension');
        $this->query->setParameter('fvExtension', $extension);
    }

    /**
     * Filters by "keywords" (which searches everything including filenames, title, tags, users who uploaded the file, tags)
     */
    public function filterByKeywords($keywords) {
        $this->query->andWhere($this->query->expr()->orX(
           $this->query->expr()->like('fv.fvFilename', ':keywords'),
           $this->query->expr()->like('fv.fvDescription', ':keywords')
        ));
        $this->query->setParameter('keywords', '%' . $keywords . '%');

        /*
        $db = Loader::db();
        $keywordsExact = $db->quote($keywords);
        $qkeywords = $db->quote('%' . $keywords . '%');

        $keys = FileAttributeKey::getSearchableIndexedList();

        foreach ($keys as $ak) {
            $cnt = $ak->getController();
            $this->query->orW
            $attribsStr.=' OR ' . $cnt->searchKeywords($keywords);
        }
        $this->filter(false, '(fvFilename like ' . $qkeywords . ' or fvDescription like ' . $qkeywords . ' or fvTitle like ' . $qkeywords . ' or fvTags like ' . $qkeywords . ' or u.uName = ' . $keywordsExact . $attribsStr . ')');
        */


    }

}