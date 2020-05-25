<?php

namespace Concrete\Controller\SinglePage\Dashboard\Files;

use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Entity\Statistics\UsageTracker\FileUsageRecord;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Navigation\Item\Item;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Doctrine\ORM\EntityManagerInterface;

class Details extends DashboardPageController
{
    protected const DOWNLOADSTATS_PAGESIZE = 3;

    public function view($fID = '')
    {
        try {
            $fileVersion = $this->getFileVersion($fID ? (int) $fID : null);
        } catch (UserMessageException $x) {
            $this->flash('error', $x->getMessage());

            return $this->buildRedirect('/dashboard/files/search');
        }
        $this->set('date', $this->app->make('date'));
        $this->set('number', $this->app->make('helper/number'));
        $this->set('resolverManager', $this->app->make(ResolverManagerInterface::class));
        $this->set('fileVersion', $fileVersion);
        $this->configureBreadcrumb($fileVersion);
        $this->configurePageTitle($fileVersion);
        $this->set('thumbnail', $fileVersion->getDetailThumbnailImage());
        $this->set('attributeKeys', $this->app->make(FileCategory::class)->getList());
        $this->set('usageRecords', $this->getUsageRecorrds($fileVersion->getFile()));
    }

    /**
     * @param int $fID
     *
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @return \Concrete\Core\Entity\File\Version
     */
    protected function getFileVersion(?int $fID): Version
    {
        $file = $fID === null ? null : $this->app->make(EntityManagerInterface::class)->find(File::class, $fID);
        if ($file === null) {
            throw new UserMessageException(t('Unable to find the requested file.'));
        }
        $permissionChecker = new Checker($file);
        if (!$permissionChecker->canViewFileInFileManager()) {
            throw new UserMessageException(t('Access denied to the requested file.'));
        }
        $fileVersion = $file->getRecentVersion() ?: $file->getApprovedVersion();
        if ($fileVersion === null) {
            throw new UserMessageException(t('The file does not have any version.'));
        }

        return $fileVersion;
    }

    protected function configureBreadcrumb(Version $fileVersion): void
    {
        $resolverManager = $this->app->make(ResolverManagerInterface::class);
        $breadcrumb = $this->createBreadcrumb();
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
        $this->set('pageTitle', $fileVersion->getFileName());
    }

    /**
     * @param \Concrete\Core\Entity\File\File $file
     *
     * @return \Concrete\Core\Entity\Statistics\UsageTracker\FileUsageRecord[]
     */
    protected function getUsageRecorrds(File $file): array
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
}
