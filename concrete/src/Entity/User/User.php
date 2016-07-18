<?php
namespace Concrete\Core\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="Users",
 *     indexes={
 *     @ORM\Index(name="uEmail", columns={"uEmail"})
 *     }
 * )
 */
class User
{
    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $uID;

    /**
     * @ORM\ManyToMany(targetEntity="\Concrete\Core\Entity\Notification\Notification", mappedBy="subscribers", cascade={"remove"})
     */
    protected $notifications;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     */
    protected $uName;

    /**
     * @ORM\Column(type="string", length=254)
     */
    protected $uEmail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $uPassword;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $uIsActive = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $uIsFullRecord = true;

    /**
     * @ORM\Column(type="boolean", options={"default"=-1})
     */
    protected $uIsValidated = -1;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $uDateAdded = null;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $uLastPasswordChange = null;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $uHasAvatar = false;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
     */
    protected $uLastOnline = 0;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
     */
    protected $uLastLogin = 0;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"unsigned": true, "default": 0})
     */
    protected $uPreviousLogin = 0;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
     */
    protected $uNumLogins = 0;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
     */
    protected $uLastAuthTypeID = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $uLastIP;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $uTimezone;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    protected $uDefaultLanguage;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $uIsPasswordReset = false;


    public function __construct()
    {
        $this->uLastPasswordChange = new \DateTime();
        $this->uDateAdded = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getUserID()
    {
        return $this->uID;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->uName;
    }

    /**
     * @return mixed
     */
    public function getUserEmail()
    {
        return $this->uEmail;
    }

    /**
     * @return mixed
     */
    public function getUserPassword()
    {
        return $this->uPassword;
    }

    /**
     * @return mixed
     */
    public function isUserActive()
    {
        return $this->uIsActive;
    }

    /**
     * @return mixed
     */
    public function isUserFullRecord()
    {
        return $this->uIsFullRecord;
    }

    /**
     * @return mixed
     */
    public function isUserValidated()
    {
        return $this->uIsValidated;
    }

    /**
     * @return mixed
     */
    public function getUserDateAdded()
    {
        return $this->uDateAdded;
    }

    /**
     * @return mixed
     */
    public function getUserLastPasswordChange()
    {
        return $this->uLastPasswordChange;
    }

    /**
     * @return mixed
     */
    public function userHasAvatar()
    {
        return $this->uHasAvatar;
    }

    /**
     * @return mixed
     */
    public function getUserLastOnline()
    {
        return $this->uLastOnline;
    }

    /**
     * @return mixed
     */
    public function getUserLastLogin()
    {
        return $this->uLastLogin;
    }

    /**
     * @return mixed
     */
    public function getUserPreviousLogin()
    {
        return $this->uPreviousLogin;
    }

    /**
     * @return mixed
     */
    public function getUserTotalLogins()
    {
        return $this->uNumLogins;
    }

    /**
     * @return mixed
     */
    public function getUserLastAuthenticationTypeID()
    {
        return $this->uLastAuthTypeID;
    }

    /**
     * @return mixed
     */
    public function getUserLastIP()
    {
        return $this->uLastIP;
    }

    /**
     * @return mixed
     */
    public function getUserTimezone()
    {
        return $this->uTimezone;
    }

    /**
     * @return mixed
     */
    public function getUserDefaultLanguage()
    {
        return $this->uDefaultLanguage;
    }

    /**
     * @return mixed
     */
    public function isUserPasswordReset()
    {
        return $this->uIsPasswordReset;
    }

    /**
     * @param mixed $uID
     */
    public function setUserID($uID)
    {
        $this->uID = $uID;
    }

    /**
     * @param mixed $uName
     */
    public function setUserName($uName)
    {
        $this->uName = $uName;
    }

    /**
     * @param mixed $uEmail
     */
    public function setUserEmail($uEmail)
    {
        $this->uEmail = $uEmail;
    }

    /**
     * @param mixed $uPassword
     */
    public function setUserPassword($uPassword)
    {
        $this->uPassword = $uPassword;
    }

    /**
     * @param mixed $uIsActive
     */
    public function setUserIsActive($uIsActive)
    {
        $this->uIsActive = $uIsActive;
    }

    /**
     * @param mixed $uIsFullRecord
     */
    public function setUserIsFullRecord($uIsFullRecord)
    {
        $this->uIsFullRecord = $uIsFullRecord;
    }

    /**
     * @param mixed $uIsValidated
     */
    public function setUserIsValidated($uIsValidated)
    {
        $this->uIsValidated = $uIsValidated;
    }

    /**
     * @param mixed $uDateAdded
     */
    public function setUserDateAdded($uDateAdded)
    {
        $this->uDateAdded = $uDateAdded;
    }

    /**
     * @param mixed $uLastPasswordChange
     */
    public function setUserLastPasswordChange($uLastPasswordChange)
    {
        $this->uLastPasswordChange = $uLastPasswordChange;
    }

    /**
     * @param mixed $uHasAvatar
     */
    public function setUserHasAvatar($uHasAvatar)
    {
        $this->uHasAvatar = $uHasAvatar;
    }

    /**
     * @param mixed $uLastOnline
     */
    public function setUserLastOnline($uLastOnline)
    {
        $this->uLastOnline = $uLastOnline;
    }

    /**
     * @param mixed $uLastLogin
     */
    public function setUserLastLogin($uLastLogin)
    {
        $this->uLastLogin = $uLastLogin;
    }

    /**
     * @param mixed $uPreviousLogin
     */
    public function setUserPreviousLogin($uPreviousLogin)
    {
        $this->uPreviousLogin = $uPreviousLogin;
    }

    /**
     * @param mixed $uNumLogins
     */
    public function setUserTotalLogins($uNumLogins)
    {
        $this->uNumLogins = $uNumLogins;
    }

    /**
     * @param mixed $uLastAuthTypeID
     */
    public function setUserLastAuthenticationTypeID($uLastAuthTypeID)
    {
        $this->uLastAuthTypeID = $uLastAuthTypeID;
    }

    /**
     * @param mixed $uLastIP
     */
    public function setUserLastIP($uLastIP)
    {
        $this->uLastIP = $uLastIP;
    }

    /**
     * @param mixed $uTimezone
     */
    public function setUserTimezone($uTimezone)
    {
        $this->uTimezone = $uTimezone;
    }

    /**
     * @param mixed $uDefaultLanguage
     */
    public function setUserDefaultLanguage($uDefaultLanguage)
    {
        $this->uDefaultLanguage = $uDefaultLanguage;
    }

    /**
     * @param mixed $uIsPasswordReset
     */
    public function setUserIsPasswordReset($uIsPasswordReset)
    {
        $this->uIsPasswordReset = $uIsPasswordReset;
    }

    public function getUserInfoObject()
    {
        return \UserInfo::getByID($this->getUserID());
    }





}
