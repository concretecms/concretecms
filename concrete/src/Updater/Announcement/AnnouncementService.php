<?php

namespace Concrete\Core\Updater\Announcement;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Application\UserInterface\Welcome\Modal\Slide\SlideInterface;
use Concrete\Core\Entity\Update\Announcement;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;

class AnnouncementService implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    const NAMESPACE_CORE = 'concrete';
    const PREFIX_VERSION = 'version';
    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function registerCoreVersion(string $version)
    {
        $version = self::PREFIX_VERSION . $version;
        $handle = preg_replace(
            '/[^A-Za-z0-9]/i',
            '',
            $version
        );

        $introduction = new Announcement();
        $introduction->setNamespace(self::NAMESPACE_CORE);
        $introduction->setHandle($handle);
        $this->entityManager->persist($introduction);
        $this->entityManager->flush();
    }

    public function getAnnouncementDriver(Announcement $announcement): AnnouncementInterface
    {
        if ($announcement->getNamespace() == self::NAMESPACE_CORE) {
            $class = 'Concrete\\Core\\Updater\\Announcement\\Announcement\\' .
                ucfirst($announcement->getHandle());
            return $this->app->make($class);
        }
        throw new \Exception(
            t(
                'Unable to locate driver for announcement %s:%s',
                $announcement->getNamespace(),
                $announcement->getHandle()
            )
        );
    }

    /**
     * Given the specified user, return an array of update announcement objects
     * to display for that user.
     *
     * @return SlideInterface[]
     */
    public function getAnnouncementSlidesForUser(User $user): array
    {
        $announcements = $this->entityManager->getRepository(Announcement::class)
            ->findAll();
        $slides = [];
        foreach ($announcements as $announcement) {
            $slides = array_merge($slides, $announcement->getSlides());
        }
        return $slides;
    }

}
