<?php
namespace Concrete\Core\User;

use Concrete\Core\Application\Application;
use Doctrine\ORM\EntityManagerInterface;

class UserInfoRepository
{
    protected $entityManager;
    protected $application;

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
     * @deprecated
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
     * @param string $uHash
     * @param bool $unredeemedHashesOnly
     *
     * @return UserInfo|null
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

    private function get($where, $var)
    {
        $repository = $this->entityManager->getRepository('Concrete\Core\Entity\User\User');
        $entity = $repository->findOneBy(array($where => $var));
        if (is_object($entity)) {
            $ui = $this->application->make('Concrete\Core\User\UserInfo');
            $ui->setEntityObject($entity);
            return $ui;
        }
    }
}
