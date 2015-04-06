<?php
namespace Concrete\Core\Application\Service\UserInterface\Help;

class Message
{
    protected $content;

    protected $guideToLaunch = null;

    protected $identifier;

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param mixed $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return null
     */
    public function getGuideToLaunch()
    {
        return $this->guideToLaunch;
    }

    /**
     * @param null $guideToLaunch
     */
    public function setGuideToLaunch($guideToLaunch)
    {
        $this->guideToLaunch = $guideToLaunch;
    }


}
