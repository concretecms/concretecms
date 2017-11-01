<?php
namespace Concrete\Authentication\Google;

defined('C5_EXECUTE') or die('Access Denied');

use Concrete\Core\Authentication\LoginException;
use Concrete\Core\Authentication\Type\Google\Factory\GoogleServiceFactory;
use Concrete\Core\Authentication\Type\OAuth\OAuth2\GenericOauth2TypeController;
use OAuth\OAuth2\Service\Google;
use Concrete\Core\User\User;
use Concrete\Core\Routing\RedirectResponse;

class Controller extends GenericOauth2TypeController
{
    public function supportsRegistration()
    {
        return \Config::get('auth.google.registration.enabled', false);
    }

    public function registrationGroupID()
    {
        return \Config::get('auth.google.registration.group');
    }

    public function getAuthenticationTypeIconHTML()
    {
        return '<i class="fa fa-google"></i>';
    }

    public function getHandle()
    {
        return 'google';
    }

    /**
     * @return Google
     */
    public function getService()
    {
        if (!$this->service) {
            /** @var GoogleServiceFactory $factory */
            $factory = $this->app->make(GoogleServiceFactory::class);
            $this->service = $factory->createService();
        }

        return $this->service;
    }

    public function saveAuthenticationType($args)
    {
        $config = $this->app->make('config');
        $config->save('auth.google.appid', $args['apikey']);
        $config->save('auth.google.secret', $args['apisecret']);
        $config->save('auth.google.registration.enabled', (bool) $args['registration_enabled']);
        $config->save('auth.google.registration.group', intval($args['registration_group'], 10));

        $whitelist = [];
        foreach (explode(PHP_EOL, $args['whitelist']) as $entry) {
            $whitelist[] = trim($entry);
        }

        $blacklist = [];
        foreach (explode(PHP_EOL, $args['blacklist']) as $entry) {
            $blacklist[] = json_decode(trim($entry), true);
        }

        $config->save('auth.google.email_filters.whitelist', array_values(array_filter($whitelist)));
        $config->save('auth.google.email_filters.blacklist', array_values(array_filter($blacklist)));
    }

    public function edit()
    {
        $config=$this->app->make('config');
        $this->set('form', $this->app->make('helper/form'));
        $this->set('apikey', $config->get('auth.google.appid', ''));
        $this->set('apisecret', $config->get('auth.google.secret', ''));

        $list = new \GroupList();
        $list->includeAllGroups();
        $this->set('groups', $list->getResults());

        $this->set('whitelist', $config->get('auth.google.email_filters.whitelist', []));
        $blacklist = array_map(function ($entry) {
            return json_encode($entry);
        }, $config->get('auth.google.email_filters.blacklist', []));

        $this->set('blacklist', $blacklist);
    }

    public function completeAuthentication(User $u)
    {
        $ui = \UserInfo::getByID($u->getUserID());
        if (!$ui->hasAvatar()) {
            try {
                $image = \Image::open($this->getExtractor()->getImageURL());
                $ui->updateUserAvatar($image);
            } catch (\Imagine\Exception\InvalidArgumentException $e) {
                \Log::addNotice("Unable to fetch user images in Google Authentication Type, is allow_url_fopen disabled?");
            } catch (\Exception $e) {
            }
        }

        return parent::completeAuthentication($u);
    }

    public function isValid()
    {
        $filters = (array) $this->app->make('config')->get('auth.google.email_filters', []);
        $domain = $this->getExtractor()->getExtra('domain');

        foreach (array_get($filters, 'whitelist', []) as $regex) {
            if (preg_match($regex, $domain)) {
                return true;
            }
        }

        foreach (array_get($filters, 'blacklist', []) as $arr) {
            list($regex, $error) = array_pad((array) $arr, 2, null);
            if (preg_match($regex, $domain)) {
                if (trim($error)) {
                    throw new LoginException($error);
                }

                return false;
            }
        }

        return true;
    }

    public function handle_detach_attempt()
    {

        if (!User::isLoggedIn()) {
            $response = new RedirectResponse(\URL::to('/login'), 302);
            $response->send();
            exit;
        }
        $user = new User();
        $uID = $user->getUserID();
        $namespace = $this->getHandle();


        $binding = $this->getBindingForUser($user);
        $accessToken = $this->getService()
            ->getStorage()
            ->retrieveAccessToken(
                $this->getService()->service()
            )
            ->getAccessToken();
        $this->getService()->request('https://accounts.google.com/o/oauth2/revoke?token='.$accessToken, 'GET');
        try {
            /* @var \Concrete\Core\Database\Connection\Connection $database */
            $database = $this->app->make('database')->connection();
            $database->delete('OauthUserMap', ['user_id' => $uID, 'namespace' => $namespace, 'binding' => $binding]);
            $this->showSuccess(t('Successfully detached.'));

            exit;
        } catch (\Exception $e) {
            \Log::error(t('Deattach Error %s', $e->getMessage()));
            $this->showError(t('Unable to detach account.'));
            exit;
        }
    }
}
