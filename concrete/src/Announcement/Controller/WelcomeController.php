<?php

namespace Concrete\Core\Announcement\Controller;

use Concrete\Core\Announcement\Controller\Traits\SingleSlideTrait;
use Concrete\Core\Announcement\Item\Factory\WelcomeItemFactory;
use Concrete\Core\Announcement\Modal\ModalInterface;
use Concrete\Core\Announcement\Slide\Slide;
use Concrete\Core\Announcement\Slide\SlideInterface;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\User\User;
use Concrete\Core\User\User as ConcreteUser;
use Concrete\Core\Validation\CSRF\Token;

class WelcomeController extends AbstractController
{

    use SingleSlideTrait;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var WelcomeItemFactory
     */
    protected $itemFactory;

    /**
     * @var Token
     */
    protected $token;

    /**
     * @param Repository $config
     */
    public function __construct(Token $token, Repository $config, WelcomeItemFactory $itemFactory)
    {
        $this->token = $token;
        $this->itemFactory = $itemFactory;
        $this->config = $config;
    }

    public function getSlide(User $user): SlideInterface
    {
        return new Slide('concrete-announcement-welcome-slide', [
            'itemAccessToken' => $this->token->generate('view_help'),
            'items' => $this->itemFactory->getItems(true),
        ]);
    }


}
