<?php 
namespace Concrete\Core\File;
use Concrete\Core\Foundation\Collection\DatabaseItemList;
use Database;
use Doctrine\DBAL\Logging\EchoSQLLogger;
use Doctrine\DBAL\Query;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use \Concrete\Core\Pagination\Pagination;
use FileAttributeKey;

class FileList extends DatabaseItemList
{

    const PERMISSION_LEVEL_IGNORE = -1;
    const PERMISSION_LEVEL_VIEW = 0;
    const PERMISSION_LEVEL_VIEW_IN_FILE_MANAGER = 5;

    /**
     * Columns in this array can be sorted via the request.
     * @var array
     */
    protected $autoSortColumns = array('fvFilename', 'fvAuthorName','fvTitle', 'fDateAdded', 'fvDateAdded', 'fvSize');

    /**
     * The default permission level is to view ALL files and ignore permissions.
     * @var int
     */
    protected $permissionLevel = self::PERMISSION_LEVEL_IGNORE;

    public function createQuery()
    {
        $this->query->select('f.fID')
            ->from('Files', 'f')
            ->innerJoin('f', 'FileVersions', 'fv', 'f.fID = fv.fID and fv.fvIsApproved = 1')
            ->leftJoin('f', 'FileSearchIndexAttributes', 'fsi', 'f.fID = fsi.fID')
            ->leftJoin('f', 'Users', 'u', 'f.uID = u.uID');
    }

    public function getTotalResults()
    {
        $query = clone $this->query;
        return $query->select('count(distinct f.fID)')->setMaxResults(1)->execute()->fetchColumn();
    }

    public function getPagination()
    {
        $adapter = new DoctrineDbalAdapter($this->query, function($query) {
            $query->select('count(distinct f.fID)')->setMaxResults(1);
        });
        if ($this->permissionLevel == self::PERMISSION_LEVEL_IGNORE) {
            $pagination = new Pagination($this, $adapter);
        } else {
            $pagination = new FuzzyPagination($this, $adapter);
        }
        return $pagination;
    }

    /**
     * @param $queryRow
     * @return \Concrete\Core\File\File
     */
    public function getResult($queryRow)
    {
        $f = File::getByID($queryRow['fID']);
        if (is_object($f)) {
            if ($this->permissionLevel == self::PERMISSION_LEVEL_IGNORE) {
                return $f;
            }
        }
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
     * Filters by "keywords" (which searches everything including filenames,
     * title, users who uploaded the file, tags)
     */
    public function filterByKeywords($keywords) {
        $expressions = array(
            $this->query->expr()->like('fv.fvFilename', ':keywords'),
            $this->query->expr()->like('fv.fvDescription', ':keywords'),
            $this->query->expr()->like('fv.fvTitle', ':keywords'),
            $this->query->expr()->like('fv.fvTags', ':keywords'),
            $this->query->expr()->eq('uName', ':keywords')
        );

        $keys = FileAttributeKey::getSearchableIndexedList();
        foreach ($keys as $ak) {
            $cnt = $ak->getController();
            $expressions[] = $cnt->searchKeywords($keywords, $this->query);
        }
        $expr = $this->query->expr();
        $this->query->andWhere(call_user_func_array(array($expr, 'orX'), $expressions));
        $this->query->setParameter('keywords', '%' . $keywords . '%');
    }


    public function filterBySet($fs) {
        $table = 'fsf' . $fs->getFileSetID();
        $this->query->leftJoin('f', 'FileSetFiles', $table, 'f.fID = ' . $table . '.fID');
        $this->query->andWhere($table . '.fsID = :fsID' . $fs->getFileSetID());
        $this->query->setParameter('fsID' . $fs->getFileSetID(), $fs->getFileSetID());
    }

    public function filterByNoSet()
    {
        $this->query->leftJoin('f', 'FileSetFiles', 'fsex', 'f.fID = fsex.fID');
        $this->query->andWhere('fsex.fsID is null');
    }

    /**
     * Filters the file list by file size (in kilobytes)
     */
    public function filterBySize($from, $to)
    {
        $this->query->andWhere('fv.fvSize >= :fvSizeFrom');
        $this->query->andWhere('fv.fvSize <= :fvSizeTo');
        $this->query->setParameter('fvSizeFrom', $from * 1024);
        $this->query->setParameter('fvSizeTo', $to * 1024);
    }

    /**
     * Filters by public date
     * @param string $date
     */
    public function filterByDateAdded($date, $comparison = '=')
    {
        $this->query->andWhere('f.fDateAdded ' . $comparison . ' :fDateAdded');
        $this->query->setParameter('fDateAdded', $date);
    }

    public function filterByOriginalPageID($ocID)
    {
        $this->query->andWhere('f.ocID = :ocID');
        $this->query->setParameter('ocID', $ocID);
    }

    /**
     * filters a FileList by the uID of the approving User
     * @param int $uID
     * @return void
     */
    public function filterByApproverUserID($uID)
    {
        $this->query->andWhere('fv.fvApproverUID = :fvApproverUID');
        $this->query->setParameter('fvApproverUID', $uID);
    }

    /**
     * filters a FileList by the uID of the owning User
     * @param int $uID
     * @return void
     * @since 5.4.1.1+
     */
    public function filterByAuthorUserID($uID)
    {
        $this->query->andWhere('fv.fvAuthorUID = :fvAuthorUID');
        $this->query->setParameter('fvAuthorUID', $uID);
    }

    /**
     * Filters by "tags" only.
     */
    public function filterByTags($tags)
    {
        $this->query->andWhere($this->query->expr()->andX(
            $this->query->expr()->like('fv.fvTags', ':tags')
        ));
        $this->query->setParameter('tags', '%' . $tags . '%');
    }

    /**
     * Sorts by filename in ascending order.
     */
    public function sortByFilenameAscending()
    {
        $this->query->orderBy('fv.fvFilename', 'asc');
    }


}