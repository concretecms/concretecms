<?php

namespace Concrete\Authentication\Google;

defined('C5_EXECUTE') or die('Access Denied');

use Concrete\Core\Authentication\LoginException;
use Concrete\Core\Authentication\Type\Google\Factory\GoogleServiceFactory;
use Concrete\Core\Authentication\Type\OAuth\OAuth2\GenericOauth2TypeController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Form\Service\Widget\GroupSelector;
use Concrete\Core\Routing\RedirectResponse;
use Concrete\Core\User\Group\GroupRepository;
use Concrete\Core\User\User;
use Concrete\Core\Utility\Service\Validation\Strings;
use OAuth\OAuth2\Service\Google;

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
        return '<i class="fab fa-google"></i>';
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

        $stringsValidator = $this->app->make(Strings::class);

        $whitelist = [];
        foreach (preg_split('/\s*[\r\n]\s*/', array_get($args, 'whitelist', ''), -1, PREG_SPLIT_NO_EMPTY) as $entry) {
            if (!$stringsValidator->isValidRegex($entry)) {
                throw new UserMessageException(t('The regular expression "%s" is not valid.', $entry));
            }
            $whitelist[] = $entry;
        }

        $blacklist = [];
        foreach (preg_split('/\s*[\r\n]\s*/', array_get($args, 'blacklist', ''), -1, PREG_SPLIT_NO_EMPTY) as $entry) {
            set_error_handler(function () {}, -1);
            $decoded = @json_decode($entry, true);
            restore_error_handler();
            if (!is_array($decoded) || !isset($decoded[0])) {
                throw new UserMessageException(t('The black list line "%s" is not valid.', $entry));
            }
            if (!$stringsValidator->isValidRegex($decoded[0])) {
                throw new UserMessageException(t('The regular expression "%s" is not valid.', $entry));
            }
            $blacklist[] = $decoded;
        }

        $config->save('auth.google.appid', (string) ($args['apikey'] ?? ''));
        $config->save('auth.google.secret', (string) ($args['apisecret'] ?? ''));
        $config->save('auth.google.registration.enabled', !empty($args['registration_enabled']));
        $config->save('auth.google.registration.group', ((int) ($args['registration_group'] ?? 0)) ?: null);
        $config->save('auth.google.email_filters.whitelist', $whitelist);
        $config->save('auth.google.email_filters.blacklist', $blacklist);
    }

    public function edit()
    {
        $config = $this->app->make('config');
        $this->set('form', $this->app->make('helper/form'));
        $this->set('groupSelector', $this->app->make(GroupSelector::class));
        $this->set('apikey', (string) $config->get('auth.google.appid', ''));
        $this->set('apisecret', (string) $config->get('auth.google.secret', ''));
        $this->set('registrationEnabled', (bool) $config->get('auth.google.registration.enabled'));
        $registrationGroupID = (int) $config->get('auth.google.registration.group');
        $registrationGroup = $registrationGroupID === 0 ? null : $this->app->make(GroupRepository::class)->getGroupById($registrationGroupID);
        $this->set('registrationGroup', $registrationGroup === null ? null : (int) $registrationGroup->getGroupID());
        $this->set('whitelist', (array) $config->get('auth.google.email_filters.whitelist', []));
        $blacklist = array_map(function ($entry) {
            return json_encode($entry);
        }, (array) $config->get('auth.google.email_filters.blacklist', []));
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
                $this->logger->notice('Unable to fetch user images in Google Authentication Type, is allow_url_fopen disabled?');
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
        $user = $this->app->make(User::class);
        if (!$user->isRegistered()) {
            $response = new RedirectResponse(\URL::to('/login'), 302);
            $response->send();
            exit;
        }
        $uID = $user->getUserID();
        $namespace = $this->getHandle();

        $binding = $this->getBindingForUser($user);
        $accessToken = $this->getService()
            ->getStorage()
            ->retrieveAccessToken(
                $this->getService()->service()
            )
            ->getAccessToken()
        ;
        $this->getService()->request('https://accounts.google.com/o/oauth2/revoke?token=' . $accessToken, 'GET');
        try {
            $this->getBindingService()->clearBinding($uID, $binding, $namespace, true);
            $this->showSuccess(t('Successfully detached.'));

            exit;
        } catch (\Exception $e) {
            \Log::error(t('Detach Error %s', $e->getMessage()));
            $this->showError(t('Unable to detach account.'));
            exit;
        }
    }
}
