<?php

namespace Concrete\Controller\SinglePage\Dashboard\Files;

use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\Entity\File\DownloadStatistics;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Entity\Statistics\UsageTracker\FileUsageRecord;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\Command\RescanFileAsyncCommand;
use Concrete\Core\File\Command\RescanFileCommand;
use Concrete\Core\File\Rescanner;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Navigation\Item\Item;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class Details extends DashboardPageController
{

    protected function setupPage(?Version $fileVersion)
    {
        $this->set('date', $this->app->make('date'));
        $this->set('number', $this->app->make('helper/number'));
        $this->set('resolverManager', $this->app->make(ResolverManagerInterface::class));
        $this->set('fileVersion', $fileVersion);
        $this->set('filePermissions', new Checker($fileVersion->getFile()));
        $this->configureBreadcrumb($fileVersion);
        $this->configurePageTitle($fileVersion);
        $this->set('thumbnail', $fileVersion->getDetailThumbnailImage());
        $this->set('attributeKeys', $this->app->make(FileCategory::class)->getList());
        $this->set('usageRecords', $this->getUsageRecords($fileVersion->getFile()));
        $this->set('recentDownloads', $this->getRecentDownloads($fileVersion->getFile()));
        $this->set('headerMenu', $this->app->make(ElementManager::class)->get('dashboard/files/header',
            ['file' => $fileVersion->getFile()]
        ));

    }

    public function view($fID = '')
    {
        try {
            $fileVersion = $this->getFileVersion($fID, null);
            $this->setupPage($fileVersion);
        } catch (UserMessageException $x) {
            $this->flash('error', $x->getMessage());
            return $this->buildRedirect('/dashboard/files/search');
        }
    }

    public function preview_version($fID = null, $fvID = null)
    {
        try {
            $fileVersion = $this->getFileVersion($fID, $fvID);
            $this->setupPage($fileVersion);
        } catch (UserMessageException $x) {
            $this->flash('error', $x->getMessage());
            return $this->buildRedirect('/dashboard/files/search');
        }
    }

    public function rescan($fID = '')
    {
        $fileVersion = $this->getFileVersion($fID ? (int) $fID : null);

        if (!$this->token->validate("ccm-filedetails-rescan-{$fID}", $this->request->request->get('token'))) {
            throw new \RuntimeException($this->token->getErrorMessage());
        }

        $rescanFileCommand = new RescanFileAsyncCommand($fID);
        $this->app->executeCommand($rescanFileCommand);

        $this->flash('success', t('The file has been rescanned.'));

        return new JsonResponse($fileVersion);
    }

    /**
     * @param int $fID
     *
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @return \Concrete\Core\Entity\File\Version
     */
    protected function getFileVersion(int $fID = null, int $fvID = null): Version
    {
        $file = $fID === null ? null : $this->app->make(EntityManagerInterface::class)->find(File::class, $fID);
        if ($file === null) {
            throw new UserMessageException(t('Unable to find the requested file.'));
        }
        $permissionChecker = new Checker($file);
        if (!$permissionChecker->canViewFileInFileManager()) {
            throw new UserMessageException(t('Access denied to the requested file.'));
        }
        if ($fvID) {
            $fileVersion = $file->getVersion($fvID);
        }
        if (!isset($fileVersion)) {
            $fileVersion = $file->getApprovedVersion() ?: $file->getRecentVersion();
        }
        if ($fileVersion === null) {
            throw new UserMessageException(t('The file does not have any version.'));
        }

        return $fileVersion;
    }

    protected function configureBreadcrumb(Version $fileVersion): void
    {
        $resolverManager = $this->app->make(ResolverManagerInterface::class);
        $factory = $this->createBreadcrumbFactory();
        $breadcrumb = $factory->getBreadcrumb(Page::getByPath('/dashboard/files/search'));
        $items = [new Item('#', $fileVersion->getFileName())];
        $folder = $fileVersion->getFile()->getFileFolderObject();
        while ($folder instanceof FileFolder && ($parentFolder = $folder->getTreeNodeParentObject()) instanceof FileFolder) {
            $folderUrl = $resolverManager->resolve(['/dashboard/files/search/folder', $folder->getTreeNodeID()]);
            $items[] = new Item((string) $folderUrl, $folder->getTreeNodeDisplayName('text'));
            $folder = $parentFolder;
        }
        while (($item = array_pop($items)) !== null) {
            $breadcrumb->add($item);
        }
        $this->setBreadcrumb($breadcrumb);
    }

    protected function configurePageTitle(Version $fileVersion): void
    {
        $this->set('pageTitle', $fileVersion->getTitle());
    }

    /**
     * @param \Concrete\Core\Entity\File\File $file
     *
     * @return \Concrete\Core\Entity\Statistics\UsageTracker\FileUsageRecord[]
     */
    protected function getUsageRecords(File $file): array
    {
        $em = $this->app->make(EntityManagerInterface::class);
        $repo = $em->getRepository(FileUsageRecord::class);
        $dictionary = [];
        foreach ($repo->findByFile($file) as $record) {
            $key = "{$record->getCollectionId()}@{$record->getCollectionVersionId()}";
            if (!isset($dictionary[$key])) {
                $dictionary[$key] = $record;
            }
        }
        ksort($dictionary);

        return array_values($dictionary);
    }

    /**
     * @param \Concrete\Core\Entity\File\File $file
     *
     * @return \Concrete\Core\Entity\File\DownloadStatistics[]
     */
    protected function getRecentDownloads(File $file, int $maxRecords = 5): array
    {
        $em = $this->app->make(EntityManagerInterface::class);
        $qb = $em->createQueryBuilder()
            ->from(DownloadStatistics::class, 'ds')
            ->select('ds')
            ->andWhere($em->getExpressionBuilder()->eq('ds.file', ':file'))
            ->orderBy('ds.downloadDateTime', 'DESC')
        ;
        if ($maxRecords > 0) {
            $qb->setMaxResults($maxRecords);
        }

        return $qb->getQuery()->execute(['file' => $file]);
    }
}
