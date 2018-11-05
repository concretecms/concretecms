<?php

namespace Concrete\Core\User;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\User\User as UserEntity;
use Concrete\Core\Entity\User\UserSignup;
use Concrete\Core\Notification\Type\UserSignupType;
use Concrete\Core\User\Event\AddUser;
use Concrete\Core\User\Event\UserInfoWithPassword;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\Phpass\PasswordHash;

class RegistrationService implements RegistrationServiceInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var UserInfoRepository
     */
    protected $userInfoRepository;

    public function __construct(Application $application, EntityManagerInterface $entityManager, UserInfoRepository $userInfoRepository)
    {
        $this->application = $application;
        $this->entityManager = $entityManager;
        $this->userInfoRepository = $userInfoRepository;
    }

    /**
     * @param string $uPasswordEncrypted
     * @param string $uEmail
     *
     * @return UserInfo|null
     */
    public function createSuperUser($uPasswordEncrypted, $uEmail)
    {
        $dh = $this->application->make('date');

        $entity = new UserEntity();
        $entity->setUserID(USER_SUPER_ID);
        $entity->setUserName(USER_SUPER);
        $entity->setUserEmail($uEmail);
        $entity->setUserPassword($uPasswordEncrypted);
        $entity->setUserIsActive(true);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->userInfoRepository->getByID($entity->getUserID());
    }

    /**
     * @param array $data
     *
     * @return UserInfo|false|null
     */
    public function create($data)
    {
        $uae = new AddUser($data);
        $uae = \Events::dispatch('on_before_user_add', $uae);
        if (!$uae->proceed()) {
            return false;
        }

        $config = $this->application->make('config');
        $hasher = new PasswordHash($config->get('concrete.user.password.hash_cost_log2'), $config->get('concrete.user.password.hash_portable'));

        if (isset($data['uIsValidated']) && $data['uIsValidated'] == 1) {
            $uIsValidated = 1;
        } elseif (isset($data['uIsValidated']) && $data['uIsValidated'] == 0) {
            $uIsValidated = 0;
        } else {
            $uIsValidated = -1;
        }

        if (isset($data['uIsFullRecord']) && $data['uIsFullRecord'] == 0) {
            $uIsFullRecord = 0;
        } else {
            $uIsFullRecord = 1;
        }

        $password_to_insert = isset($data['uPassword']) ? $data['uPassword'] : null;
        $hash = $hasher->HashPassword($password_to_insert);

        $uDefaultLanguage = null;
        if (isset($data['uDefaultLanguage']) && $data['uDefaultLanguage'] != '') {
            $uDefaultLanguage = $data['uDefaultLanguage'];
        }

        $entity = new UserEntity();
        $entity->setUserName($data['uName']);
        $entity->setUserEmail($data['uEmail']);
        $entity->setUserPassword($hash);
        $entity->setUserIsValidated($uIsValidated);
        $entity->setUserIsFullRecord($uIsFullRecord);
        $entity->setUserDefaultLanguage($uDefaultLanguage);
        $entity->setUserIsActive(true);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $newUID = $entity->getUserID();
        $ui = $this->userInfoRepository->getByID($newUID);

        if (is_object($ui)) {
            $uo = $ui->getUserObject();
            $groupControllers = \Group::getAutomatedOnRegisterGroupControllers($uo);
            foreach ($groupControllers as $ga) {
                if ($ga->check($uo)) {
                    $uo->enterGroup($ga->getGroupObject());
                }
            }

            // run any internal event we have for user add
            $ue = new UserInfoWithPassword($ui);
            $ue->setUserPassword($password_to_insert);
            \Events::dispatch('on_user_add', $ue);

            // Now we notify any relevant users.
            $type = $this->application->make('manager/notification/types')->driver('user_signup');
            /* @var UserSignupType $type */
            $u = new User();
            $createdBy = null;
            if (is_object($u)) {
                $creator = $u->getUserInfoObject();
                if (is_object($creator)) {
                    $createdBy = $creator->getEntityObject();
                }
            }
            $signup = new UserSignup($ui->getEntityObject(), $createdBy);
            $notifier = $type->getNotifier();
            $subscription = $type->getSubscription($signup);
            $notified = $notifier->getUsersToNotify($subscription, $signup);
            $notification = $type->createNotification($signup);
            $notifier->notify($notified, $notification);
        }

        return $ui;
    }

    /**
     * @param array $data
     *
     * @return UserInfo
     */
    public function createFromPublicRegistration($data)
    {
        // slightly different than add. this is public facing
        $config = $this->application->make('config');
        if ($config->get('concrete.user.registration.validate_email')) {
            $data['uIsValidated'] = 0;
        }
        $ui = $this->create($data);

        return $ui;
    }

    /**
     * Create an unused username starting from user details.
     *
     * @param string $email The user's email address
     * @param string $suggestedUsername A suggestion about the username
     * @param string $firstName The user's first name
     * @param string $lastName The user's last name
     *
     * @return string
     */
    public function getNewUsernameFromUserDetails($email, $suggestedUsername = '', $firstName = '', $lastName = '')
    {
        $baseUsername = $this->stringToUsernameChunk($suggestedUsername);
        if ($baseUsername === '') {
            $firstName = $this->stringToUsernameChunk($firstName);
            $lastName = $this->stringToUsernameChunk($lastName);
            if ($firstName !== '' || $lastName !== '') {
                $baseUsername = trim($firstName . '_' . $lastName, '_');
            } else {
                $mailbox = strstr((string) $email, '@', true);
                $baseUsername = $this->stringToUsernameChunk($mailbox);
            }
            if ($baseUsername === '') {
                $baseUsername = 'user';
            }
        }
        $username = $baseUsername;
        $suffix = 1;
        while ($this->userInfoRepository->getByName($username) !== null) {
            $username = $baseUsername . '_' . $suffix;
            ++$suffix;
        }

        return $username;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function stringToUsernameChunk($string)
    {
        $string = trim((string) $string);
        $string = preg_replace('/[^a-z0-9]+/', '_', strtolower($string));
        $string = preg_replace('/__+/', '_', $string);
        $string = trim($string, '_');

        return $string;
    }
}
