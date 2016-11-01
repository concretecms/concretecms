<?php
namespace Concrete\Core\Conversation\Message;

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
        } elseif ($this->author->getName()) {
            $name = $this->author->getName();
        } else {
            $name = t('Anonymous');
        }
        if (is_object($ui) && ($profileURL = $ui->getUserPublicProfileUrl())) {
            return sprintf('<a href="%s">%s</a>', $profileURL, h($name));
        } elseif ($this->author->getWebsite()) {
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
        } elseif ($this->author->getName()) {
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
        if (is_object($ui)) {
            return $ui->getUserAvatar()->output();
        } else {
            $av = \Core::make('Concrete\Core\User\Avatar\AnonymousAvatar');
            $av->setAlt($this->author->getName());
            return $av->output();
        }
    }
}
