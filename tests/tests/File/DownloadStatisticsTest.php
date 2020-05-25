<?php

namespace Concrete\Tests\File;

use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\DownloadStatistics;
use Concrete\Core\File\DownloadStatistics\Download;
use Concrete\TestHelpers\File\FileStorageTestCase;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class DownloadStatisticsTest extends FileStorageTestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->tables = array_merge($this->tables, [
            'DownloadStatistics',
        ]);
    }

    public function testDownloadStatistics()
    {
        $downloadStatistics = app(DownloadStatistics::class);
        $em = app(EntityManagerInterface::class);

        $list = $downloadStatistics->getDownloads(null, null, null);
        $this->assertSame([], $list->getList());
        $this->assertSame(false, $list->hasMoreDownloads());

        $fv1 = $this->addFile($em);
        $fv2 = $this->addFile($em);

        $id = $downloadStatistics->trackDownload($fv2);
        $this->assertGreaterThan(0, $id);
        $this->assertInstanceOf(Download::class, $downloadStatistics->getDownloadByID($id));

        $list = $downloadStatistics->getDownloads(null, null, 2);
        $this->assertSame(1, count($list->getList()));
        $this->assertSame(false, $list->hasMoreDownloads());

        $downloadStatistics->trackDownload($fv2);

        $list = $downloadStatistics->getDownloads(null, null, 2);
        $this->assertSame(2, count($list->getList()));
        $this->assertSame(false, $list->hasMoreDownloads());

        $downloadStatistics->trackDownload($fv1);

        $list = $downloadStatistics->getDownloads(null, null, 2);
        $this->assertSame(2, count($list->getList()));
        $this->assertSame(true, $list->hasMoreDownloads());

        $list = $downloadStatistics->getDownloads($fv2->getFile()->getFileID(), null, 2);
        $this->assertSame(2, count($list->getList()));
        $this->assertSame(false, $list->hasMoreDownloads());

        $list = $downloadStatistics->getDownloads($fv2->getFile()->getFileID(), $fv2->getFileVersionID(), 2);
        $this->assertSame(2, count($list->getList()));
        $this->assertSame(false, $list->hasMoreDownloads());

        $list = $downloadStatistics->getDownloads($fv2->getFile()->getFileID(), $fv2->getFileVersionID() + 100, 2);
        $this->assertSame(0, count($list->getList()));
        $this->assertSame(false, $list->hasMoreDownloads());
    }

    private function addFile(EntityManagerInterface $em): Version
    {
        $f = new File();
        $f->setDateAdded(new DateTime());
        $fv = new Version();
        $fv->setFilename('test.txt');
        $fv->setFile($f);
        $f->getFileVersions()->add($fv);
        $em->persist($f);
        $em->persist($fv);
        $em->flush();

        return $fv;
    }
}
