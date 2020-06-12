<?php

namespace Concrete\Controller\Dialog\File;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Csv\Export\DownloadStatisticsExporter;
use Concrete\Core\Csv\WriterFactory;
use Concrete\Core\Entity\File\DownloadStatistics;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\Validation\CSRF\Token;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Statistics extends Controller
{
    /**
     * @var int|null
     */
    protected const RECORDS_PER_PAGE = 50;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/file/statistics';

    /**
     * @param int|mixed $fID
     *
     * @throws \Concrete\Core\Error\UserMessageException
     */
    public function view($fID): void
    {
        $file = $this->getFile($fID);
        if ($file === null) {
            throw new UserMessageException(t('Unable to find the requested file.'));
        }
        $permissionChecker = new Checker($file);
        if (!$permissionChecker->canViewFileInFileManager()) {
            throw new UserMessageException(t('Access denied to the requested file.'));
        }
        $this->set('resolver', $this->app->make(ResolverManagerInterface::class));
        $this->set('token', $this->app->make(Token::class));
        $this->set('file', $file);
        list($records, $hasMoreRecords) = $this->loadRecords($file, static::RECORDS_PER_PAGE);
        $this->set('records', $records);
        $this->set('hasMoreRecords', $hasMoreRecords);
        $this->set('totalRecords', $file->getTotalDownloads());
    }

    public function load_more($fID = ''): JsonResponse
    {
        $token = $this->app->make(Token::class);
        if (!$token->validate("ccm-file-statistics-more-{$fID}")) {
            throw new UserMessageException($token->getErrorMessage());
        }
        $file = $this->getFile($fID);
        $beforeID = (int) $this->request->request->get('beforeID');
        if ($beforeID <= 0) {
            throw new UserMessageException(t('Invalid arguments.'));
        }
        list($records, $hasMoreRecords) = $this->loadRecords($file, static::RECORDS_PER_PAGE, $beforeID);

        return $this->app->make(ResponseFactoryInterface::class)->json([
            'records' => $records,
            'hasMoreRecords' => $hasMoreRecords,
        ]);
    }

    public function download($fID = ''): Response
    {
        $token = $this->app->make(Token::class);
        if (!$token->validate("ccm-file-statistics-download-{$fID}")) {
            throw new UserMessageException($token->getErrorMessage());
        }
        $file = $this->getFile($fID);
        $config = $this->app->make('config');
        $bom = $config->get('concrete.export.csv.include_bom') ? $config->get('concrete.charset_bom') : '';
        $writer = $this->app->make(
            DownloadStatisticsExporter::class,
            [
                'file' => $file,
                'writer' => $this->app->make(WriterFactory::class)->createFromPath('php://output', 'w'),
            ]
        );

        return StreamedResponse::create(
            function () use ($bom, $writer) {
                echo $bom;
                $writer->insertHeaders()->insertRecords();
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="Downloads of ' . $file->getApprovedVersion()->getFileName() . '.csv"',
            ]
        );
    }

    protected function loadRecords(File $file, ?int $max, ?int $beforeID = null): array
    {
        $em = $this->app->make(EntityManagerInterface::class);
        $qb = $em->createQueryBuilder();
        $qb
            ->from(DownloadStatistics::class, 'ds')
            ->select('ds')
            ->andWhere($qb->expr()->eq('ds.file', ':file'))
            ->setParameter('file', $file)
            ->addOrderBy('ds.downloadDateTime', 'DESC')
        ;
        if ($max !== null) {
            $qb->setMaxResults($max + 1);
        }
        if ($beforeID != null) {
            $qb
                ->andWhere($qb->expr()->lt('ds.id', ':beforeID'))
                ->setParameter('beforeID', $beforeID)
            ;
        }
        $records = [];
        $hasMoreRecords = false;
        $date = $this->app->make(Date::class);
        $userInfoRepository = $this->app->make(UserInfoRepository::class);
        foreach ($qb->getQuery()->execute() as $downloadStatistics) {
            if ($max !== null && isset($records[$max - 1])) {
                $hasMoreRecords = true;
                break;
            }
            $records[] = $this->formatRecord($downloadStatistics, $date, $userInfoRepository);
        }

        return [
            $records,
            $hasMoreRecords,
        ];
    }

    protected function formatRecord(DownloadStatistics $record, Date $date, UserInfoRepository $userInfoRepository): array
    {
        $result = [
            'id' => $record->getID(),
            'dt' => $date->formatPrettyDateTime($record->getDownloadDateTime(), true, true),
            'v' => $record->getFileVersion(),
            'p' => $record->getRelatedPageID() ? (string) Page::getCollectionPathFromID($record->getRelatedPageID()) : null,
        ];
        if ($record->getDownloaderID()) {
            $userInfo = $userInfoRepository->getByID($record->getDownloaderID());
            $result['u'] = $userInfo ? $userInfo->getUserName() : $record->getDownloaderID();
        }

        return $result;
    }

    /**
     * @param int|mixed $fID
     *
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @return \Concrete\Core\Entity\File\File
     */
    protected function getFile($fID): File
    {
        $file = $fID ? $this->app->make(EntityManagerInterface::class)->find(File::class, (int) $fID) : null;
        if ($file === null) {
            throw new UserMessageException(t('Unable to find the requested file.'));
        }
        $permissionChecker = new Checker($file);
        if (!$permissionChecker->canViewFileInFileManager()) {
            throw new UserMessageException(t('Access denied to the requested file.'));
        }

        return $file;
    }
}
