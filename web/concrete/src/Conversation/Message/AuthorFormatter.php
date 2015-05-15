<?php
namespace Concrete\Core\Conversation\Message;

use Concrete\Core\User\UserInfo;

class AuthorFormatter
{

    protected $author;

    public function __construct(Author $author)
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        $ui = $this->author->getUser();
        if (is_object($ui)) {
            $name = $ui->getUserDisplayName();
        } else if ($this->author->getName()) {
            $name = $this->author->getName();
        } else {
            $name = t('Anonymous');
        }
        if ($this->author->getWebsite()) {
            return sprintf('<a href="%s">%s</a>', h($this->author->getWebsite()), h($name));
        } else {
            return h($name);
        }
    }

    /**
     * @return string
     */
    public function getLinkedAdministrativeDisplayName()
    {
        $ui = $this->author->getUser();
        $html = '<a href="%s">%s</a>';
        if (is_object($ui)) {
            $link = \URL::to('/dashboard/users/search', 'view', $ui->getUserID());
            $name = $ui->getUserDisplayName();
        } else if ($this->author->getName()) {
            $link = 'mailto:' . h($this->author->getEmail());
            $name = h($this->author->getName());
        } else {
            return t('Anonymous');
        }
        return sprintf($html, $link, $name);
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        $ui = $this->author->getUser();
        $useGravatars = \Config::get('concrete.user.gravatar.enabled');
        $av = \Core::make('helper/concrete/avatar');
        if (is_object($ui) || !$useGravatars) {
            return $av->outputUserAvatar($ui);
        } else {
            // we try to use the gravatar with the author email address.
            return '<img src="' . $av->getGravatar($this->author->getEmail()) . '" />';
        }
    }
}
