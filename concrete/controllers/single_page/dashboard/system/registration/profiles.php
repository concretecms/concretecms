<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;

use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Page\Single;

class Profiles extends DashboardSitePageController
{
    public $helpers = ['form'];

    public function update_profiles()
    {
        /** @var Token $token */
        $token = \Core::make('token');

        if (!$token->validate("update_profile")) {
            $this->error->add('Invalid Token.');

            return;
        }

        if ($this->isPost()) {
            $config = $this->getSite()->getConfigRepository();

            $isProfileOptionChanged = (bool) $config->get('user.profiles_enabled') !== (bool) $this->post('public_profiles');

            $config->save('user.profiles_enabled', ($this->post('public_profiles') ? true : false));
            $config->save('user.gravatar.enabled', ($this->post('gravatar_fallback') ? true : false));
            $config->save('user.gravatar.max_level', $this->app->make('helper/security')->sanitizeString($this->post('gravatar_max_level')));
            $config->save('user.gravatar.image_set', $this->app->make('helper/security')->sanitizeString($this->post('gravatar_image_set')));
            $config->save('user.display_account_menu', (bool) $this->post('display_account_menu', false));


            if (!$isProfileOptionChanged) {
                $this->flash('success', t('Public profiles settings have been updated.'));
                $this->redirect('/dashboard/system/registration/profiles');
            }

            // $message = ($this->post('public_profiles')?t('Public profiles have been enabled'):t('Public profiles have been disabled.'));
            if ($this->post('public_profiles')) {
                Single::add('/members');
                $c = Single::add('/members/profile');
                Single::add('/members/directory');
                $c->update(['cName' => 'View Profile']);

                $this->redirect('/dashboard/system/registration/profiles/profiles_enabled');
            } else {
                foreach ($this->app->make('site')->getList() as $site) {
                    foreach (['/members/directory', '/members/profile', '/members'] as $path) {
                        $c = \Page::getByPath($path, 'RECENT', $site);
                        $c->delete();
                    }
                }
                $this->redirect('/dashboard/system/registration/profiles/profiles_disabled');
            }
        }
    }

    public function profiles_enabled()
    {
        $this->set('message', t('Public profiles have been enabled. Public profile and directory single pages added to the sitemap.'));
        $this->view();
    }

    public function profiles_disabled()
    {
        $this->set('message', t('Public profiles have been disabled. Public profile and directory single pages removed from the sitemap'));
        $this->view();
    }

    public function view($message = null)
    {
        if ($message) {
            $this->set('message', $message);
        }

        $config = $this->getSite()->getConfigRepository();

        $this->set('public_profiles', $config->get('user.profiles_enabled'));
        $this->set('gravatar_fallback', $config->get('user.gravatar.enabled'));
        $this->set('gravatar_max_level', $config->get('user.gravatar.max_level'));
        $this->set('gravatar_level_options', ['g' => 'G', 'pg' => 'PG', 'r' => 'R', 'x' => 'X']);
        $this->set('gravatar_image_set', $config->get('user.gravatar.image_set'));
        $this->set('gravatar_set_options', ['404' => '404', 'mm' => 'mm', 'identicon' => 'identicon', 'monsterid' => 'monsterid', 'wavatar' => "wavatar"]);
        $this->set('display_account_menu', $config->get('user.display_account_menu'));
    }
}
