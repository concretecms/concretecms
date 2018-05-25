<?php

namespace Concrete\Core\Install;

use Concrete\Core\Cache\OpCache;
use Concrete\Core\Config\Renderer;
use Concrete\Core\Error\UserMessageException;
use DateTimeZone;
use Exception;
use Illuminate\Filesystem\Filesystem;

class InstallerOptions
{
    /**
     * The Filesystem instance to use.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * If the database already exists and is valid, lets just attach to it rather than installing over it?
     *
     * @var bool
     */
    protected $autoAttachEnabled = false;


    /**
     * Whether the user has accepted the privacy policy from the front-end installation
     *
     * @var bool
     */
    protected $privacyPolicyAccepted = false;

    /**
     * The installation configuration options (persisted as /application/config/site_install.php).
     *
     * @var array
     */
    protected $configuration = [];

    /**
     * The admin user email.
     *
     * @var string
     */
    protected $userEmail = '';

    /**
     * The admin password hash.
     *
     * @var string
     */
    protected $userPasswordHash;

    /**
     * The handle of the starting point.
     *
     * @var string
     */
    protected $startingPointHandle = '';

    /**
     * The name of the site.
     *
     * @var string
     */
    protected $siteName = '';

    /**
     * The identifier of the site locale.
     *
     * @var string
     */
    protected $siteLocaleId = '';

    /**
     * The identifier of the UI locale.
     *
     * @var string
     */
    protected $uiLocaleId = '';

    /**
     * The server time zone identifier.
     *
     * @var string
     */
    protected $serverTimeZoneId = '';

    /**
     * Initializes the instance.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }



    /**
     * If the database already exists and is valid, lets just attach to it rather than installing over it?
     *
     * @return bool
     */
    public function isAutoAttachEnabled()
    {
        return $this->autoAttachEnabled;
    }

    /**
     * If the database already exists and is valid, lets just attach to it rather than installing over it?
     *
     * @param bool $value
     *
     * @return $this;
     */
    public function setAutoAttachEnabled($value)
    {
        $this->autoAttachEnabled = (bool) $value;

        return $this;
    }

    /**
     * Get the installation configuration options (persisted as /application/config/site_install.php).
     *
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Set the installation configuration options (persisted as /application/config/site_install.php).
     *
     * @param array $value
     *
     * @return $this
     */
    public function setConfiguration(array $value)
    {
        $this->configuration = $value;

        return $this;
    }

    /**
     * Get the admin user email.
     *
     * @return string
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    /**
     * Set the admin user email.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setUserEmail($value)
    {
        $this->userEmail = (string) $value;

        return $this;
    }

    /**
     * Get the admin password hash.
     *
     * @return string
     */
    public function getUserPasswordHash()
    {
        return $this->userPasswordHash;
    }

    /**
     * Set the admin password hash.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setUserPasswordHash($value)
    {
        $this->userPasswordHash = (string) $value;

        return $this;
    }

    /**
     * Returns whether privacy policy is accepted
     *
     * @return boolean
     */
    public function isPrivacyPolicyAccepted()
    {
        return $this->privacyPolicyAccepted;
    }

    /**
     * Set the privacy policy status
     *
     * @param boolean $privacyPolicyAccepted
     */
    public function setPrivacyPolicyAccepted($privacyPolicyAccepted)
    {
        $this->privacyPolicyAccepted = $privacyPolicyAccepted;

        return $this;
    }


    /**
     * Get the handle of the starting point.
     *
     * @return string
     */
    public function getStartingPointHandle()
    {
        return $this->startingPointHandle;
    }

    /**
     * Set the handle of the starting point.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setStartingPointHandle($value)
    {
        $this->startingPointHandle = (string) $value;

        return $this;
    }

    /**
     * Get the name of the site.
     *
     * @return string
     */
    public function getSiteName()
    {
        return $this->siteName;
    }

    /**
     * Get the name of the site.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setSiteName($value)
    {
        $this->siteName = (string) $value;

        return $this;
    }

    /**
     * Get the identifier of the site locale.
     *
     * @return string
     */
    public function getSiteLocaleId()
    {
        return $this->siteLocaleId;
    }

    /**
     * Set the identifier of the site locale.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setSiteLocaleId($value)
    {
        $this->siteLocaleId = (string) $value;

        return $this;
    }

    /**
     * Get the identifier of the UI locale.
     *
     * @return string
     */
    public function getUiLocaleId()
    {
        return $this->uiLocaleId;
    }

    /**
     * Set the identifier of the UI locale.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setUiLocaleId($value)
    {
        $this->uiLocaleId = (string) $value;

        return $this;
    }

    /**
     * Get the server time zone identifier.
     *
     * @return string
     */
    public function getServerTimeZoneId()
    {
        return $this->serverTimeZoneId;
    }

    /**
     * Set the server time zone identifier.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setServerTimeZoneId($value)
    {
        $this->serverTimeZoneId = (string) $value;

        return $this;
    }

    /**
     * Get the server time zone instance.
     *
     * @param bool $fallbackToDefault Fallback to the default one if the time zone is not defined?
     *
     * @throws UserMessageException
     *
     * @return DateTimeZone
     */
    public function getServerTimeZone($fallbackToDefault)
    {
        $timeZoneId = $this->getServerTimeZoneId();
        if ($timeZoneId === '') {
            if (!$fallbackToDefault) {
                throw new UserMessageException(t('The server time zone has not been defined.'));
            }
            $timeZoneId = @date_default_timezone_get() ?: 'UTC';
        }
        try {
            $result = new DateTimeZone($timeZoneId);
        } catch (Exception $x) {
            $result = null;
        }
        if ($result === null) {
            throw new UserMessageException(t('Invalid server time zone: %s', $timeZoneId));
        }

        return $result;
    }

    /**
     * Do the configuration files exist?
     *
     * @return bool
     */
    public function hasConfigurationFiles()
    {
        return $this->filesystem->isFile(DIR_CONFIG_SITE . '/site_install.php') && $this->filesystem->isFile(DIR_CONFIG_SITE . '/site_install_user.php');
    }

    /**
     * Load the configuration options from file.
     *
     * @throws UserMessageException
     */
    public function load()
    {
        if (!$this->filesystem->isFile(DIR_CONFIG_SITE . '/site_install.php')) {
            throw new UserMessageException(t('File %s could not be found.', DIRNAME_APPLICATION . '/' . DIRNAME_CONFIG . '/site_install.php'));
        }
        if (!$this->filesystem->isFile(DIR_CONFIG_SITE . '/site_install_user.php')) {
            throw new UserMessageException(t('File %s could not be found.', DIRNAME_APPLICATION . '/' . DIRNAME_CONFIG . '/site_install_user.php'));
        }
        $siteInstall = $this->filesystem->getRequire(DIR_CONFIG_SITE . '/site_install.php');
        if (!is_array($siteInstall)) {
            throw new UserMessageException(t('The file %s contains invalid data.', DIRNAME_APPLICATION . '/' . DIRNAME_CONFIG . '/site_install.php'));
        }
        $siteInstallUser = @$this->filesystem->getRequire(DIR_CONFIG_SITE . '/site_install_user.php');
        if (is_array($siteInstallUser)) {
            $siteInstallUser += [
                'startingPointHandle' => '',
                'uiLocaleId' => '',
                'serverTimeZone' => '',
            ];
        } else {
            if (!(
                defined('INSTALL_USER_EMAIL')
                && defined('INSTALL_USER_PASSWORD_HASH')
                && defined('SITE')
                && defined('SITE_INSTALL_LOCALE')
            )) {
                throw new UserMessageException(t('The file %s contains invalid data.', DIRNAME_APPLICATION . '/' . DIRNAME_CONFIG . '/site_install_user.php'));
            }
            $siteInstallUser = [
                'userEmail' => INSTALL_USER_EMAIL,
                'userPasswordHash' => INSTALL_USER_PASSWORD_HASH,
                'startingPointHandle' => defined('INSTALL_STARTING_POINT') ? INSTALL_STARTING_POINT : '',
                'siteName' => SITE,
                'siteLocaleId' => SITE_INSTALL_LOCALE,
                'uiLocaleId' => defined('APP_INSTALL_LANGUAGE') ? APP_INSTALL_LANGUAGE : '',
                'serverTimeZone' => defined('INSTALL_TIMEZONE') ? INSTALL_TIMEZONE : '',
            ];
        }
        $this
            ->setPrivacyPolicyAccepted($siteInstallUser['privacyPolicy'])
            ->setConfiguration($siteInstall)
            ->setUserEmail($siteInstallUser['userEmail'])
            ->setUserPasswordHash($siteInstallUser['userPasswordHash'])
            ->setStartingPointHandle($siteInstallUser['startingPointHandle'])
            ->setSiteName($siteInstallUser['siteName'])
            ->setSiteLocaleId($siteInstallUser['siteLocaleId'])
            ->setUiLocaleId($siteInstallUser['uiLocaleId'])
            ->setServerTimeZoneId($siteInstallUser['serverTimeZone'])
        ;
    }

    /**
     * Save the configuration options to file.
     *
     * @throws UserMessageException
     */
    public function save()
    {
        $render = new Renderer($this->configuration);
        $siteInstall = $render->render();
        $render = new Renderer([
            'userEmail' => $this->getUserEmail(),
            'userPasswordHash' => $this->getUserPasswordHash(),
            'startingPointHandle' => $this->getStartingPointHandle(),
            'siteName' => $this->getSiteName(),
            'siteLocaleId' => $this->getSiteLocaleId(),
            'uiLocaleId' => $this->getUiLocaleId(),
            'serverTimeZone' => $this->getServerTimeZoneId(),
            'privacyPolicy' => $this->isPrivacyPolicyAccepted()
        ]);
        $siteInstallUser = $render->render();
        if (@$this->filesystem->put(DIR_CONFIG_SITE . '/site_install.php', $siteInstall) === false) {
            throw new UserMessageException(t('Failed to write to file %s', DIRNAME_APPLICATION . '/' . DIRNAME_CONFIG . '/site_install.php'));
        }
        OpCache::clear(DIR_CONFIG_SITE . '/site_install_user.php');
        if (@$this->filesystem->put(DIR_CONFIG_SITE . '/site_install_user.php', $siteInstallUser) === false) {
            throw new UserMessageException(t('Failed to write to file %s', DIRNAME_APPLICATION . '/' . DIRNAME_CONFIG . '/site_install_user.php'));
        }
        OpCache::clear(DIR_CONFIG_SITE . '/site_install_user.php');
    }

    /**
     * Delete the configuration files (if they exist).
     */
    public function deleteFiles()
    {
        $files = [];
        foreach ([
            DIR_CONFIG_SITE . '/site_install.php',
            DIR_CONFIG_SITE . '/site_install_user.php',
        ] as $file) {
            if ($this->filesystem->isFile($file)) {
                OpCache::clear($file);
                $files[] = $file;
            }
        }
        if (!empty($files)) {
            $this->filesystem->delete($files);
        }
    }
}
