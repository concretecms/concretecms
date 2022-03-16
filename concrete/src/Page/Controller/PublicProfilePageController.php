<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use PageController as CorePageController;
use Config;

class PublicProfilePageController extends CorePageController implements UsesFeatureInterface
{

    public function getRequiredFeatures(): array
    {
        return [
            Features::PROFILE
        ];
    }

    public function on_start()
    {
        parent::on_start();

        $site = \Core::make('site')->getSite();
        $config = $site->getConfigRepository();

        if (!$config->get('user.profiles_enabled')) {
            return $this->replace('/page_not_found');
        }
    }
}
