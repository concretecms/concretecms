<?php

namespace Concrete\Core\Announcement;

use Concrete\Core\Announcement\Broadcast\Broadcast;
use Concrete\Core\Announcement\Broadcast\BroadcastInterface;
use Concrete\Core\Announcement\Controller\ControllerInterface;
use Concrete\Core\Announcement\Modal\Modal;
use Concrete\Core\Announcement\Modal\ModalInterface;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Entity\Announcement\Announcement as AnnouncementEntity;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Announcement\Announcement\Announcement;

class AnnouncementService implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Checker
     */
    protected $checker;

    /**
     * @var User
     */
    protected $user;

    public function __construct(EntityManager $entityManager, Checker $checker, User $user)
    {
        $this->entityManager = $entityManager;
        $this->checker = $checker;
        $this->user = $user;
    }

    public function createAnnouncement(string $handle): AnnouncementEntity
    {
        $announcement = new AnnouncementEntity();
        $announcement->setHandle($handle);
        $this->entityManager->persist($announcement);
        $this->entityManager->flush();

        return $announcement;
    }

    /**
     * @return AnnouncementEntity[]
     */
    protected function getAnnouncementsForUser(User $user): array
    {
        return $this->entityManager->getRepository(AnnouncementEntity::class)
            ->findBy([], ['id' => 'asc']);
    }

    public function getBroadcast(): ?BroadcastInterface
    {
        if ($this->checker->canViewAnnouncementContent()) {
            $broadcast = new Broadcast();
            $announcements = $this->getAnnouncements();
            foreach ($announcements as $announcement) {
                $controller = $announcement->getController();
                if ($controller->showAnnouncement($this->user, $announcements)) {
                    $slides = $controller->getSlides($this->user);
                    if (count($slides)) {
                        $announcementComponent = $controller->createAnnouncementComponent($announcement, $slides);
                        $broadcast->addAnnouncement($announcementComponent);
                    }
                }
            }
            if (count($broadcast->getAnnouncements()) > 0) {
                return $broadcast;
            }
        }
        return null;

    }

    public function markAnnouncementAsViewed(AnnouncementEntity $announcement, User $user)
    {

    }
}
