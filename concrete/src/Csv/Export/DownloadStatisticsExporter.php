<?php

namespace Concrete\Core\Csv\Export;

use Concrete\Core\Entity\File\DownloadStatistics;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Page\Page;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use League\Csv\Writer;

defined('C5_EXECUTE') or die('Access Denied.');

class DownloadStatisticsExporter
{
    /**
     * @var string
     */
    protected const DATETIME_OUTPUT_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var int
     */
    protected const MAX_ITEMS_PER_QUERY = 50;

    /**
     * @var \Concrete\Core\Entity\File\File
     */
    protected $file;

    /**
     * @var \League\Csv\Writer
     */
    protected $writer;

    /**
     * @var \DateTimeZone
     */
    protected $appTimezone;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @param \Concrete\Core\Entity\File\File $file
     * @param \League\Csv\Writer $writer
     * @param \Concrete\Core\Localization\Service\Date $dateService
     */
    public function __construct(File $file, Writer $writer, Date $dateService, EntityManagerInterface $entityManager)
    {
        $this->file = $file;
        $this->writer = $writer;
        $this->appTimezone = $dateService->getTimezone('app');
        $this->entityManager = $entityManager;
    }

    /**
     * Insert the header row.
     *
     * @return $this
     */
    public function insertHeaders(): self
    {
        $this->writer->insertOne(iterator_to_array($this->generateHeaders()));

        return $this;
    }

    /**
     * Insert all the record rows.
     *
     * @return $this
     */
    public function insertRecords(): self
    {
        foreach ($this->generateRecordList() as $record) {
            $this->writer->insertOne(iterator_to_array($this->generateRecord($record)));
        }

        return $this;
    }

    /**
     * @return string[]
     */
    protected function generateHeaders(): Generator
    {
        yield 'id';
        yield 'Download date/time';
        yield 'File version';
        yield 'Downloader ID';
        yield 'Downloader name';
        yield 'Page ID';
        yield 'Page path';
    }

    /**
     * @return \Concrete\Core\Entity\File\DownloadStatistics[]
     */
    protected function generateRecordList(): Generator
    {
        $repository = $this->entityManager->getRepository(DownloadStatistics::class);
        $qb = $repository->createQueryBuilder('ds');
        $qb
            ->andWhere($qb->expr()->eq('ds.file', ':file'))->setParameter('file', $this->file)
            ->orderBy('ds.id', 'ASC')
            ->setMaxResults(static::MAX_ITEMS_PER_QUERY)
        ;
        for (;;) {
            $afterRecord = null;
            foreach ($qb->getQuery()->execute() as $record) {
                $afterRecord = $record;
                yield $record;
            }
            if ($afterRecord === null) {
                break;
            }
            if ($qb->getParameter('afterID') === null) {
                $qb->andWhere($qb->expr()->gt('ds.id', ':afterID'));
            }
            $qb->setParameter('afterID', $afterRecord->getID());
            $this->entityManager->clear(DownloadStatistics::class);
            $this->entityManager->clear(User::class);
        }
    }

    /**
     * @return \Concrete\Core\Entity\File\DownloadStatistics[]
     */
    protected function generateRecord(DownloadStatistics $record): Generator
    {
        yield $record->getID();
        yield $record->getDownloadDateTime()->setTimezone($this->appTimezone)->format(static::DATETIME_OUTPUT_FORMAT);
        yield $record->getFileVersion();
        yield $record->getDownloaderID();
        $userName = '';
        if ($record->getDownloaderID()) {
            $downloader = $this->entityManager->find(User::class, $record->getDownloaderID());
            if ($downloader !== null) {
                $userName = $downloader->getUserName();
            }
        }
        yield $userName;
        yield $record->getRelatedPageID();
        yield $record->getRelatedPageID() ? (string) Page::getCollectionPathFromID($record->getRelatedPageID()) : '';
    }
}
