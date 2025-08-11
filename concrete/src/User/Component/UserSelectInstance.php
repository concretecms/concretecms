<?php

namespace Concrete\Core\User\Component;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\User\UserInfo;
use Concrete\Core\Validation\CSRF\Token;

class UserSelectInstance
{

    /**
     * @var Repository
     */
    protected $config;

    protected $accessToken;

    protected $includeAvatar = true;

    protected $labelFormat = UserSelectInstanceFactory::LABEL_FORMAT_AUTO;

    /**
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }


    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param mixed $accessToken
     */
    public function setAccessToken($accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return bool
     */
    public function includeAvatar(): bool
    {
        return $this->includeAvatar;
    }

    /**
     * @param bool $includeAvatar
     */
    public function setIncludeAvatar(bool $includeAvatar): void
    {
        $this->includeAvatar = $includeAvatar;
    }

    /**
     * @return string
     */
    public function getLabelFormat(): string
    {
        return $this->labelFormat;
    }

    /**
     * @param string $labelFormat
     */
    public function setLabelFormat(string $labelFormat): void
    {
        $this->labelFormat = $labelFormat;
    }

    public function getLabelFormatPropValue(): string
    {
        return $this->getLabelFormat();
    }

    public function getIncludeAvatarPropValue(): string
    {
        return $this->includeAvatar() ? 'true' : 'false';
    }

    public function createResultFromUser(UserInfo $ui): array
    {
        $loginWithEmail = $this->config->get('concrete.user.registration.email_registration');
        $displayUsernameFieldWhenRegistering = $this->config->get('concrete.user.registration.display_username_field');

        // The result will _always_ include the user ID
        $data = [
            'id' => $ui->getUserID()
        ];

        if ($this->includeAvatar()) {
            $data['avatar'] = $ui->getUserAvatar()->getPath();
        }

        switch ($this->getLabelFormat()) {
            case UserSelectInstanceFactory::LABEL_FORMAT_EMAIL:
                $data['primary_label'] = $ui->getUserEmail();
                break;
            case UserSelectInstanceFactory::LABEL_FORMAT_USERNAME:
                $data['primary_label'] = $ui->getUserDisplayName();
                break;
            case UserSelectInstanceFactory::LABEL_FORMAT_USERNAME_EMAIL:
                if ($loginWithEmail) {
                    $data['primary_label'] = $ui->getUserEmail();
                    $data['secondary_label'] = $ui->getUserDisplayName();
                } else {
                    $data['primary_label'] = $ui->getUserDisplayName();
                    $data['secondary_label'] = $ui->getUserEmail();
                }
                break;
            case UserSelectInstanceFactory::LABEL_FORMAT_AUTO_MINIMUM:
                if ($loginWithEmail) {
                    $data['primary_label'] = $ui->getUserEmail();
                } else {
                    $data['primary_label'] = $ui->getUserDisplayName();
                }
                break;
            default: // UserSelectInstanceFactory::LABEL_FORMAT_AUTO
                if ($loginWithEmail) {
                    $data['primary_label'] = $ui->getUserEmail();
                    if ($displayUsernameFieldWhenRegistering) {
                        $data['secondary_label'] = $ui->getUserDisplayName();
                    }
                } else {
                    $data['primary_label'] = $ui->getUserDisplayName();
                    $data['secondary_label'] = $ui->getUserEmail();
                }
                break;
        }

        return $data;
    }


}
