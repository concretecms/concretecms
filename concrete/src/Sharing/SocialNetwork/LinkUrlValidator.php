<?php

declare(strict_types=1);

namespace Concrete\Core\Sharing\SocialNetwork;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Check the URLs of the social links before they are stored and rendered as href attributes.
 */
class LinkUrlValidator
{
    private const BUILTIN_SCHEMES = ['http', 'https', 'mailto', 'tel'];

    /**
     * Schemes the browsers execute: never allowed, whatever the configuration says.
     */
    private const FORBIDDEN_SCHEMES = ['javascript', 'data', 'vbscript'];

    /**
     * @var string[]
     */
    private $additionalSchemes = [];

    /**
     * @param array<string|mixed>] $additionalSchemes the schemes accepted besides http, https, mailto and tel (see the concrete.social.additional_schemes configuration key)
     */
    public function __construct(array $additionalSchemes = [])
    {
        foreach ($additionalSchemes as $scheme) {
            $scheme = is_string($scheme) ? strtolower(trim($scheme)) : '';
            if (preg_match('/^[a-z][a-z0-9+.\-]*$/D', $scheme) === 1 && !in_array($scheme, self::FORBIDDEN_SCHEMES, true)) {
                $this->additionalSchemes[] = $scheme;
            }
        }
    }

    /**
     * @return string[] lower case
     */
    public function getAllowedSchemes(): array
    {
        return array_values(array_unique(array_merge(self::BUILTIN_SCHEMES, $this->additionalSchemes)));
    }

    public function getInvalidUrlMessage(string $url): string
    {
        return t('"%1$s" is not a valid URL: the allowed schemes are %2$s.', $url, implode(', ', $this->getAllowedSchemes()));
    }

    /**
     * @param string|mixed $url
     */
    public function isValid($url): bool
    {
        if (!is_string($url) || preg_match('/^(?<scheme>[a-z][a-z0-9+.\-]*):(?<value>.*)$/iD', $url, $matches) !== 1) {
            return false;
        }
        $scheme = strtolower($matches['scheme']);
        switch ($scheme) {
            case 'http':
            case 'https':
                return filter_var($url, FILTER_VALIDATE_URL) !== false;
            case 'mailto':
                if (preg_match('/[\x00-\x1f\x7f]/', $matches['value']) === 1) {
                    return false;
                }
                $address = explode('?', $matches['value'], 2)[0];

                return filter_var($address, FILTER_VALIDATE_EMAIL) !== false;
            case 'tel':
                return preg_match('/^\+?[0-9][0-9.\-()]*(;[a-z0-9.\-=]+)*$/iD', $matches['value']) === 1;
            default:
                // The syntax of the configured schemes is unknown: only the characters that break an attribute are rejected
                return in_array($scheme, $this->additionalSchemes, true) && preg_match('/[\x00-\x1f\x7f]/', $matches['value']) !== 1;
        }
    }
}
