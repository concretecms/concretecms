<?php

namespace Concrete\Core\Announcement;

use Concrete\Core\Announcement\Broadcast\Broadcast;
use Concrete\Core\Announcement\Broadcast\BroadcastInterface;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Entity\Announcement\Announcement;
use Concrete\Core\Entity\Announcement\Announcement as AnnouncementEntity;
use Concrete\Core\Entity\Announcement\AnnouncementUserView;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;

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

    public function createAnnouncementIfNotExists(string $handle): AnnouncementEntity
    {
        $announcement = $this->entityManager->getRepository(AnnouncementEntity::class)
            ->findOneByHandle($handle);
        if (!$announcement) {
            $announcement = new AnnouncementEntity();
            $announcement->setHandle($handle);
            $this->entityManager->persist($announcement);
            $this->entityManager->flush();
        }
        return $announcement;
    }

    /**
     * @return AnnouncementEntity[]
     */
    protected function getAnnouncementsForUser(User $user): array
    {
        $db = $this->entityManager->getConnection();
        // Return announcements that this user has NOT seen.
        $r = $db->executeQuery(
            'select id from Announcements a left join AnnouncementUserViews v 
    on (a.id = v.announcement_id and v.uID = ?) where v.uID is null',
            [
                $user->getUserID()
            ]
        );
        $announcements = [];
        while ($row = $r->fetchAssociative()) {
            $announcement = $this->entityManager->find(Announcement::class, $row['id']);
            if ($announcement
                && ($announcementController = $announcement->getController())
                && $announcementController->shouldDisplayAnnouncementToUser($user)) {
                $announcements[] = $announcement;
            }
        }
        return $announcements;
    }

    public function getBroadcast(): ?BroadcastInterface
    {
        if ($this->checker->canViewAnnouncementContent()) {
            $broadcast = new Broadcast();
            $announcements = $this->getAnnouncementsForUser($this->user);
            foreach ($announcements as $announcement) {
                $controller = $announcement->getController();
                $slides = $controller->getSlides($this->user);
                if (count($slides)) {
                    $announcementComponent = $controller->createAnnouncementComponent($announcement, $slides);
                    $broadcast->addAnnouncement($announcementComponent);
                }
            }
            if (count($broadcast->getAnnouncements()) > 0) {
                return $broadcast;
            }
        }
        return null;
    }

    public function markAnnouncementAsViewed(string $announcementHandle, User $user)
    {
        $announcement = $this->entityManager->getRepository(AnnouncementEntity::class)
            ->findOneByHandle($announcementHandle);
        $userEntity = $user->getUserInfoObject()->getEntityObject();
        if ($announcement) {
            $this->entityManager->createQueryBuilder()
                ->delete(AnnouncementUserView::class, 'v')
                ->andWhere('v.announcement = :announcement')
                ->andWhere('v.user = :user')
                ->setParameter('announcement', $announcement)
                ->setParameter('user', $userEntity)
                ->getQuery()
                ->execute();

            $viewed = new AnnouncementUserView();
            $viewed->setUser($userEntity);
            $viewed->setAnnouncement($announcement);
            $this->entityManager->persist($viewed);
            $this->entityManager->flush();

            $announcementController = $announcement->getController();
            $announcementController->onViewAnnouncement($user);
        } else {
            throw new \Exception(
                t('Unable to mark announcement as viewed - %s - no announcement found.', $announcementHandle)
            );
        }
    }
}
