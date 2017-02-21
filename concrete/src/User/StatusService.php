<?php
namespace Concrete\Core\User;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\User\User as UserEntity;
use Concrete\Core\Entity\User\UserSignup;
use Concrete\Core\Notification\Notifier;
use Concrete\Core\Notification\Type\UserSignupType;
use Concrete\Core\User\Event\AddUser;
use Concrete\Core\User\Event\UserInfoWithPassword;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\Phpass\PasswordHash;

class StatusService implements StatusServiceInterface
{
    protected $entityManager;
    protected $application;
    protected $userInfoRepository;

    public function __construct(Application $application, EntityManagerInterface $entityManager, UserInfoRepository $userInfoRepository)
    {
        $this->application = $application;
        $this->entityManager = $entityManager;
        $this->userInfoRepository = $userInfoRepository;
    }

    public function sendEmailValidation($user) {
      $uHash = $user->setupValidation();
      $config = $this->application->make('config');
      $mh = $this->application->make('mail');
      $fromEmail = (string) $config->get('concrete.email.validate_registration.address');
      if (strpos($fromEmail, '@')) {
          $fromName = (string) $config->get('concrete.email.validate_registration.name');
          if ($fromName === '') {
              $fromName = t('Validate Email Address');
          }
          $mh->from($fromEmail, $fromName);
      }
      $mh->addParameter('uEmail', $user->getUserEmail());
      $mh->addParameter('uHash', $uHash);
      $mh->addParameter('uEmail', $user->getUserEmail());
      $mh->addParameter('site', tc('SiteName', $config->get('concrete.site')));
      $mh->to($user->getUserEmail());
      $mh->load('validate_user_email');
      $mh->sendMail();
    }
}
