<?php
namespace Concrete\Core\Conversation\Message;

use Concrete\Core\User\UserInfo;

class Author
{

    protected $user;
    protected $name;
    protected $email;
    protected $website;

    /**
     * @return \Concrete\Core\User\UserInfo
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed
     */
    public function setUser($user)
    {
        if ($user instanceof \Concrete\Core\User\User) {
            $this->user = UserInfo::getByID($user->getUserID());
        } else {
            $this->user = $user;
        }
        $this->name = $this->user->getUserName();
        $this->email = $this->user->getUserEmail();

    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param mixed $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     *
     */
    public function getFormatter()
    {
        $formatter = new AuthorFormatter($this);
        return $formatter;
    }

}
