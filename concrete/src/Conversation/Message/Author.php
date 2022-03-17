<?php
namespace Concrete\Core\Conversation\Message;

use Concrete\Core\User\UserInfo;

class Author
{
    /** @var \Concrete\Core\User\UserInfo */
    protected $user;
    /** @var string|null */
    protected $name;
    /** @var string|null */
    protected $email;
    /** @var string|null */
    protected $website;

    /**
     * @return \Concrete\Core\User\UserInfo
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \Concrete\Core\User\User|\Concrete\Core\User\UserInfo $user
     * @return void
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
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string|null $website
     * @return void
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return AuthorFormatter
     */
    public function getFormatter()
    {
        return app(AuthorFormatter::class, ['author'=>$this]);
    }
}
