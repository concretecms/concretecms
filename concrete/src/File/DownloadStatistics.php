<?php

namespace Concrete\Core\File;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\File\Version as FileVersion;
use Concrete\Core\File\DownloadStatistics\Download;
use Concrete\Core\File\DownloadStatistics\DownloadList;
use Concrete\Core\File\Event\FileAccess as FileAccessEvent;
use Concrete\Core\User\User;
use DateTimeImmutable;
use Doctrine\DBAL\Query\QueryBuilder;
use PDO;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DownloadStatistics
{
    /**
     * @var \Concrete\Core\Database\Connection\Connection
     */
    protected $connection;

    /**
     * @var \Concrete\Core\User\User
     */
    protected $currentUser;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * @var bool|null
     */
    private $trackingEnabled;

    public function __construct(Connection $connection, User $currentUser, EventDispatcherInterface $eventDispatcher, Repository $config)
    {
        $this->connection = $connection;
        $this->currentUser = $currentUser;
        $this->eventDispatcher = $eventDispatcher;
        $this->config = $config;
    }

    /**
     * Should the downloads be tracked?
     */
    public function isTrackingEnabled(): bool
    {
        if ($this->trackingEnabled === null) {
            $this->trackingEnabled = (bool) $this->config->get('concrete.statistics.track_downloads');
        }

        return $this->trackingEnabled;
    }

    /**
     * Should the downloads be tracked?
     *
     * @return $this
     */
    public function setIsTrackingEnabled(bool $value): self
    {
        $this->trackingEnabled = $value;

        return $this;
    }

    /**
     * Method to be called whenever a file is downloaded.
     *
     * @param \Concrete\Core\Entity\File\Version $fileVersion the file version being downloaded
     * @param int|null $pageID the ID of the page where the download originated, it available
     *
     * @return int|null return the ID of the newly created record (or NULL if downloads are not tracked)
     */
    public function trackDownload(FileVersion $fileVersion, ?int $pageID = null): ?int
    {
        $fve = new FileAccessEvent($fileVersion);
        $this->eventDispatcher->dispatch('on_file_download', $fve);
        if (!$this->isTrackingEnabled()) {
            return null;
        }
        $this->connection->insert(
            'DownloadStatistics',
            [
                'fID' => $fileVersion->getFile()->getFileID(),
                'fvID' => $fileVersion->getFileVersionID(),
                'uID' => (int) $this->currentUser->getUserID(),
                'rcID' => (int) $pageID,
            ]
        );

        return (int) $this->connection->lastInsertId();
    }

    /**
     * Get a download given its ID.
     */
    public function getDownloadByID(int $dsID): ?Download
    {
        $qb = $this->connection->createQueryBuilder();
        $x = $qb->expr();
        $qb
            ->from('DownloadStatistics', 'ds')
            ->select('ds.*')
            ->andWhere($x->eq('ds.dsID', $qb->createNamedParameter($dsID)))
            ->setMaxResults(1)
        ;
        $row = $qb->execute()->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : $this->buildResult($row);
    }

    /**
     * Unlink the download statistics associated to a specific user, setting the associated user ID to an empty value.
     */
    public function resetDownloadsForUser(int $userID): void
    {
        if ($userID !== 0) {
            $this->connection->update(
                'DownloadStatistics',
                [
                    'uID' => 0,
                ],
                [
                    'uID' => $userID,
                ]
            );
        }
    }

    /**
     * Delete the statistics associated to a specific file.
     */
    public function deleteDownloadsForFile(int $fileID): void
    {
        $this->connection->delete(
            'DownloadStatistics',
            [
                'fID' => $fileID,
            ]
        );
    }

    /**
     * Get the total number of downloads of a specific file (and version).
     *
     * @param int $fileID the file ID
     * @param int|null $fileVersionID the version ID (if null, you'll get the downloads of every version)
     */
    public function getDownloadCount(int $fileID, ?int $fileVersionID = null): int
    {
        $qb = $this->connection->createQueryBuilder();
        $x = $qb->expr;
        $qb->from('DownloadStatistics', 'ds')
            ->select('count(ds.*)')
            ->andWhere($x->eq('ds.fID', $qb->createNamedParameter($fileID)))
        ;
        if ($fileVersionID !== null) {
            $qb->andWhere($x->eq('ds.fvID', $qb->createNamedParameter($fileVersionID)));
        }

        return (int) $qb->execute()->fetchColumn();
    }

    /**
     * Get the list of downloads ().
     *
     * @param int|null $fileID the ID of the file for which you want the downloads (use NULL for every file)
     * @param int|null $fileVersionID the version ID of the file for which you want the downloads (used only if $fileID is not NULL, use NULL for every version)
     * @param int|null $limit the maximum number of results (set to NULL for ALL the downloads)
     * @param int|null $afterID for paginated results, the ID of the download after which you want the results
     * @param bool $descending set to TRUE (default) for the most recent downloads to first, FALSE to have the older downloads first
     *
     * @return \Concrete\Core\File\DownloadStatistics\DownloadList
     */
    public function getDownloads(?int $fileID, ?int $fileVersionID, ?int $limit, ?int $afterID = null, bool $descending = true): DownloadList
    {
        $qb = $this->createDownloadsQuery($fileID, $fileVersionID, $limit, $afterID, $descending);
        $rs = $qb->execute();
        $result = new DownloadList();
        $count = 0;
        while (($row = $rs->fetch(PDO::FETCH_ASSOC)) !== false) {
            $count++;
            if ($limit !== null && $count > $limit) {
                $result->setHasMoreDownloads(true);
                break;
            }
            $result->add($this->buildResult($row));
        }

        return $result;
    }

    protected function createDownloadsQuery(?int $fileID, ?int $fileVersionID, ?int $limit, ?int $afterID = null, bool $descending = true): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder();
        $x = $qb->expr();
        $qb
            ->from('DownloadStatistics', 'ds')
            ->select('*')
            ->addOrderBy('ds.timestamp', $descending ? 'DESC' : 'ASC')
            ->addOrderBy('ds.dsID', $descending ? 'DESC' : 'ASC')
        ;
        if ($fileID !== null) {
            $qb->andWhere($x->eq('ds.fID', $qb->createNamedParameter($fileID)));
            if ($fileVersionID !== null) {
                $qb->andWhere($x->eq('ds.fvID', $qb->createNamedParameter($fileVersionID)));
            }
        }
        if ($limit !== null) {
            $qb->setMaxResults($limit + 1);
        }
        if ($afterID !== null) {
            $aferTimestamp = $this->getTimestamp($afterID, $fileID, $fileVersionID);
            if ($aferTimestamp === '') {
                $qb->andWhere($x->eq(1, 0));
            } else {
                $idParameter = $qb->createNamedParameter($afterID);
                $timestampParameter = $qb->createNamedParameter($aferTimestamp);
                if ($descending) {
                    $qb->andWhere($x->orX(
                        $x->lt('ds.timestamp', $timestampParameter),
                        $x->andX(
                            $x->eq('ds.timestamp', $timestampParameter),
                            $x->lt('ds.dsID', $idParameter)
                        )
                    ));
                } else {
                    $qb->andWhere($x->orX(
                        $x->gt('ds.timestamp', $timestampParameter),
                        $x->andX(
                            $x->eq('ds.timestamp', $timestampParameter),
                            $x->gt('ds.dsID', $idParameter)
                        )
                    ));
                }
            }
        }

        return $qb;
    }

    protected function getTimestamp(int $id, ?int $fileID, ?int $fileVersionID): string
    {
        $qb = $this->connection->createQueryBuilder();
        $x = $qb->expr();
        $qb
            ->from('DownloadStatistics', 'ds')
            ->select('ds.timestamp')
            ->andWhere($x->eq('ds.dsID', $qb->createNamedParameter($id)))
        ;
        if ($fileID !== null) {
            $qb->andWhere($x->eq('ds.fID', $qb->createNamedParameter($fileID)));
            if ($fileVersionID !== null) {
                $qb->andWhere($x->eq('ds.fvID', $qb->createNamedParameter($fileVersionID)));
            }
        }

        return (string) $qb->execute()->fetchColumn();
    }

    protected function buildResult(array $data): Download
    {
        return Download::create(
            (int) $data['dsID'],
            (int) $data['fID'],
            (int) $data['fvID'],
            empty($data['uID']) ? null : (int) $data['uID'],
            empty($data['rcID']) ? null : (int) $data['rcID'],
            DateTimeImmutable::createFromFormat($this->connection->getDatabasePlatform()->getDateTimeFormatString(), $data['timestamp'])
        );
    }
}
