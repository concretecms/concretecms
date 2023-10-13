<?php

namespace Concrete\Core\Install;

use Concrete\Core\Encryption\PasswordHasher;

/**
 * Configuration value object for holding all the values of a web installation. Able to generate InstallerOptions
 * objects and validate.
 */
class InstallEnvironment
{

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $startingPoint;

    /**
     * @var string
     */
    protected $siteName;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $confirmPassword;

    /**
     * @var string
     */
    protected $dbUsername;

    /**
     * @var string
     */
    protected $dbPassword;

    /**
     * @var string
     */
    protected $dbDatabase;

    /**
     * @var bool
     */
    protected $acceptPrivacyPolicy = false;

    /**
     * @var string
     */
    protected $dbServer;

    /**
     * @var string
     */
    protected $canonicalUrl;

    /**
     * @var string
     */
    protected $alternativeCanonicalUrl;

    /**
     * @var string
     */
    protected $sessionHandler;

    /**
     * @var string
     */
    protected $siteLocaleLanguage;

    /**
     * @var string
     */
    protected $siteLocaleCountry;

    /**
     * @var string
     */
    protected $timezone;

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getSiteName(): string
    {
        return $this->siteName;
    }

    /**
     * @param string $siteName
     */
    public function setSiteName(string $siteName): void
    {
        $this->siteName = $siteName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getDbDatabase(): string
    {
        return $this->dbDatabase;
    }

    /**
     * @param string $dbDatabase
     */
    public function setDbDatabase(string $dbDatabase): void
    {
        $this->dbDatabase = $dbDatabase;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getConfirmPassword(): string
    {
        return $this->confirmPassword;
    }

    /**
     * @param string $confirmPassword
     */
    public function setConfirmPassword(string $confirmPassword): void
    {
        $this->confirmPassword = $confirmPassword;
    }

    /**
     * @return string
     */
    public function getDbUsername(): string
    {
        return $this->dbUsername;
    }

    /**
     * @param string $dbUsername
     */
    public function setDbUsername(string $dbUsername): void
    {
        $this->dbUsername = $dbUsername;
    }

    /**
     * @return string
     */
    public function getDbPassword(): string
    {
        return $this->dbPassword;
    }

    /**
     * @param string $dbPassword
     */
    public function setDbPassword(string $dbPassword): void
    {
        $this->dbPassword = $dbPassword;
    }

    /**
     * @return bool
     */
    public function isAcceptPrivacyPolicy(): bool
    {
        return $this->acceptPrivacyPolicy;
    }

    /**
     * @param bool $acceptPrivacyPolicy
     */
    public function setAcceptPrivacyPolicy(bool $acceptPrivacyPolicy): void
    {
        $this->acceptPrivacyPolicy = $acceptPrivacyPolicy;
    }

    /**
     * @return string
     */
    public function getStartingPoint(): string
    {
        return $this->startingPoint;
    }

    /**
     * @param string $startingPoint
     */
    public function setStartingPoint(string $startingPoint): void
    {
        $this->startingPoint = $startingPoint;
    }

    /**
     * @return string
     */
    public function getDbServer(): string
    {
        return $this->dbServer;
    }

    /**
     * @param string $dbServer
     */
    public function setDbServer(string $dbServer): void
    {
        $this->dbServer = $dbServer;
    }

    /**
     * @return string
     */
    public function getCanonicalUrl(): ?string
    {
        return $this->canonicalUrl;
    }

    /**
     * @param string $canonicalUrl
     */
    public function setCanonicalUrl(string $canonicalUrl): void
    {
        $this->canonicalUrl = $canonicalUrl;
    }

    /**
     * @return string
     */
    public function getAlternativeCanonicalUrl(): ?string
    {
        return $this->alternativeCanonicalUrl;
    }

    /**
     * @param string $alternativeCanonicalUrl
     */
    public function setAlternativeCanonicalUrl(string $alternativeCanonicalUrl): void
    {
        $this->alternativeCanonicalUrl = $alternativeCanonicalUrl;
    }

    /**
     * @return string
     */
    public function getSessionHandler(): string
    {
        return $this->sessionHandler;
    }

    /**
     * @param string $sessionHandler
     */
    public function setSessionHandler(string $sessionHandler): void
    {
        $this->sessionHandler = $sessionHandler;
    }

    /**
     * @return string
     */
    public function getSiteLocaleLanguage(): string
    {
        return $this->siteLocaleLanguage;
    }

    /**
     * @param string $siteLocaleLanguage
     */
    public function setSiteLocaleLanguage(string $siteLocaleLanguage): void
    {
        $this->siteLocaleLanguage = $siteLocaleLanguage;
    }

    /**
     * @return string
     */
    public function getSiteLocaleCountry(): string
    {
        return $this->siteLocaleCountry;
    }

    /**
     * @param string $siteLocaleCountry
     */
    public function setSiteLocaleCountry(string $siteLocaleCountry): void
    {
        $this->siteLocaleCountry = $siteLocaleCountry;
    }

    /**
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     */
    public function setTimezone(string $timezone): void
    {
        $this->timezone = $timezone;
    }

}

