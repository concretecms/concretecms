<?php
namespace Concrete\Core\User;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\User\User as UserEntity;
use Doctrine\ORM\EntityManagerInterface;

class UserInfoRepository
{
    protected $entityManager;
    protected $application;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $repository;

    public function __construct(Application $application, EntityManagerInterface $entityManager)
    {
        $this->application = $application;
        $this->entityManager = $entityManager;
    }

    /**
     * Returns the UserInfo object for a give user's uID.
     *
     * @param int $uID
     *
     * @return UserInfo|null
     */
    public function getByID($uID)
    {
        return $this->get('uID', $uID);
    }

    /**
     * Returns the UserInfo object for a give user's username.
     *
     * @param string $uName
     *
     * @return UserInfo|null
     */
    public function getByName($uName)
    {
        return $this->get('uName', $uName);
    }

    /**
     * @deprecated Use ->getByName($name) instead
     */
    public function getByUserName($uName)
    {
        return $this->getByName($uName);
    }

    /**
     * Returns the UserInfo object for a give user's email address.
     *
     * @param string $uEmail
     *
     * @return UserInfo|null
     */
    public function getByEmail($uEmail)
    {
        return $this->get('uEmail', $uEmail);
    }

    /**
     * Get a UserInfo object from a user entity
     *
     * @param \Concrete\Core\Entity\User\User $entity
     *
     * @return \Concrete\Core\User\UserInfo
     */
    public function getByUserEntity(UserEntity $entity)
    {
        $ui = $this->application->make(UserInfo::class);
        $ui->setEntityObject($entity);

        return $ui;
    }

    /**
     * Get all users
     *
     * @param bool $activeOnly Only return active users
     *
     * @return \Concrete\Core\User\UserInfo[]
     */
    public function all($activeOnly = false)
    {
        $repository = $this->getRepository();

        // If we only want active users
        if ($activeOnly) {
            $filter['uIsActive'] = true;
            return array_map([$this, 'getByUserEntity'], $repository->findBy(['uIsActive' => true]));
        }

        return array_map([$this, 'getByUserEntity'], $repository->findAll());
    }

    /**
     * @param string $uHash
     * @param bool $unredeemedHashesOnly
     *
     * @return \Concrete\Core\User\UserInfo|null
     */
    public function getByValidationHash($uHash, $unredeemedHashesOnly = true)
    {
        $db = $this->entityManager->getConnection();
        if ($unredeemedHashesOnly) {
            $uID = $db->fetchColumn("select uID from UserValidationHashes where uHash = ? and uDateRedeemed = 0", array($uHash));
        } else {
            $uID = $db->fetchColumn("select uID from UserValidationHashes where uHash = ?", array($uHash));
        }
        if ($uID) {
            $ui = $this->getByID($uID);
            return $ui;
        }
    }

    /**
     * Resolve an single entity by
     *
     * @param $where
     * @param $var
     *
     * @return \Concrete\Core\User\UserInfo|null
     */
    private function get($where, $var)
    {
        $entity = $this->getRepository()->findOneBy(array($where => $var));

        if (!is_object($entity)) {
            return null;
        }

        return $this->getByUserEntity($entity);
    }

    /**
     * Resolve the repository instance
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository()
    {
        if (!$this->repository) {
            $this->repository = $this->entityManager->getRepository(UserEntity::class);
        }

        return $this->repository;
    }
}
