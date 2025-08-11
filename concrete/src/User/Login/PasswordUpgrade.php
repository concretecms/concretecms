<?php

namespace Concrete\Core\User\Login;

use Concrete\Core\Config\Repository\Repository;

defined('C5_EXECUTE') or die('Access Denied.');

class PasswordUpgrade
{
    public const PASSWORD_RESET_KEY = 'password_reset';
    public const PASSWORD_EXPIRED_KEY = 'password_expired';

    public const PASSWORD_EXPIRED_DAYSPLACEHOLDER = '{DAYS}';

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function getPasswordResetMessage(string $type, bool $fillPlaceholders = true): string
    {
        $message = $this->config->get("concrete.user.password.reset_message.{$type}");
        $message = is_string($message) ? trim($message) : '';
        if ($message === '') {
            $message = $this->getDefaultPasswordResetMessage($type, false);
        }
        if ($fillPlaceholders) {
            $message = $this->fillPlaceholders($type, $message);
        }

        return $message;
    }

    public function setPasswordResetMessage(string $type, string $message): void
    {
        $message = trim($message);
        if ($this->isPasswordDefaultResetMessage($type, $message)) {
            $message = '';
        }
        $this->config->set("concrete.user.password.reset_message.{$type}", $message);
        $this->config->save("concrete.user.password.reset_message.{$type}", $message);
    }

    public function getDefaultPasswordResetMessage(string $type, bool $fillPlaceholders = true): string
    {
        switch ($type) {
            case static::PASSWORD_RESET_KEY:
                $message = implode("\n", [
                    t('Your user account is being upgraded and requires a new password.'),
                    t('Please proceed to receive an email message containing the instructions about how you can reset your password.'),
                ]);
                break;
            case static::PASSWORD_EXPIRED_KEY:
                $message = implode("\n", [
                    t('Passwords must be changed at least every %1$s days.', static::PASSWORD_EXPIRED_DAYSPLACEHOLDER),
                    t('Please proceed to receive an email message containing the instructions about how you can reset your password.'),
                ]);
                break;
            default:
                $message = $type;
                break;
        }
        if ($fillPlaceholders) {
            $message = $this->fillPlaceholders($type, $message);
        }

        return $message;
    }

    protected function isPasswordDefaultResetMessage(string $type, string $message): bool
    {
        $normalize = static function (string $string): string {
            return trim(preg_replace('/[\r\n]+/', "\n", $string));
        };
        $normalizedMessage = $normalize($message);
        $normalizedDefaultMessage = $normalize($this->getDefaultPasswordResetMessage($type));

        return $normalizedMessage === $normalizedDefaultMessage;
    }

    protected function fillPlaceholders(string $type, string $message): string
    {
        switch ($type) {
            case static::PASSWORD_EXPIRED_KEY:
                $maxAge = (int) $this->config->get('concrete.user.password.max_age');
                $message = str_replace(static::PASSWORD_EXPIRED_DAYSPLACEHOLDER, (string) $maxAge, $message);
                break;
        }

        return $message;
    }
}
