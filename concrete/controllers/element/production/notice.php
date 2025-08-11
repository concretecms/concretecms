<?php
namespace Concrete\Controller\Element\Production;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Controller\ElementController;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\Traits\HandleRequiredFeaturesTrait;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Production\Modes;
use Concrete\Core\User\User;

class Notice extends ElementController implements UsesFeatureInterface
{

    use HandleRequiredFeaturesTrait;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Repository
     */
    protected $config;

    public function getRequiredFeatures(): array
    {
        return [Features::STAGING];
    }

    public function getElement()
    {
        return 'production/notice';
    }

    public function __construct(Repository $config, User $user)
    {
        $this->config = $config;
        $this->user = $user;
    }

    public function view()
    {
        $showStagingBar = false;
        if ($this->config->get('concrete.security.production.mode') === Modes::MODE_STAGING) {
            if ($this->user->isRegistered() || $this->config->get(
                    'concrete.security.production.staging.show_notification_to_unregistered_users'
                )) {
                $c = Page::getCurrentPage();
                $theme = $c->getCollectionThemeObject();
                $showStagingBar = true;
                $this->handleRequiredFeatures($this, $theme);
            }
        }
        $this->set('showStagingBar', $showStagingBar);
    }
}
