<?php

namespace Concrete\Core\Url\Validation;

use Concrete\Core\Url\Url;
use IPLib\Factory as IPFactory;
use IPLib\ParseStringFlag as IPParseStringFlag;
use IPLib\Range\Type as IPRangeType;
use RuntimeException;

class RemoteUrlValidator
{
    public function validate(string $url, array $allowedSchemes = ['http', 'https']): ValidatedRemoteUrl
    {
        try {
            $parsedUrl = Url::createFromUrl($url);
        } catch (RuntimeException $x) {
            throw new InvalidRemoteUrlException($x->getMessage(), 0, $x);
        }

        $scheme = strtolower((string) $parsedUrl->getScheme());
        if (!in_array($scheme, $allowedSchemes, true)) {
            throw new InvalidRemoteUrlException('Invalid URL scheme.');
        }

        $host = trim((string) $parsedUrl->getHost());
        if (in_array(strtolower($host), ['', '0', 'localhost'], true)) {
            throw new InvalidRemoteUrlException('Invalid URL host.');
        }

        $ipFormatBlocks = [
            '/^\d+$/',
            '/^0x[0-9a-f]+$/i',
        ];

        foreach ($ipFormatBlocks as $block) {
            if (preg_match($block, $host) !== 0) {
                throw new InvalidRemoteUrlException('Invalid URL host.');
            }
        }

        $ip = null;
        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6) !== false) {
            $ip = IPFactory::parseAddressString($host);
        } elseif (preg_match('/^[0-9.]+$/', $host) !== 0) {
            // Reject non-standard numeric host notations like dotted octal.
            throw new InvalidRemoteUrlException('Invalid URL host.');
        }

        if ($ip === null) {
            $dnsList = @dns_get_record($host, DNS_A | DNS_AAAA);
            while ($ip === null && $dnsList !== false && count($dnsList) > 0) {
                $dns = array_shift($dnsList);
                $resolvedIp = $dns['ip'] ?? $dns['ipv6'] ?? null;
                if ($resolvedIp !== null) {
                    $ip = IPFactory::parseAddressString($resolvedIp);
                }
            }
        }

        if ($ip === null || $ip->getRangeType() !== IPRangeType::T_PUBLIC) {
            throw new InvalidRemoteUrlException('Invalid URL host.');
        }

        $parsedPort = $parsedUrl->getPort();
        $port = $parsedPort ? (int) $parsedPort->get() : ($scheme === 'http' ? 80 : 443);

        return new ValidatedRemoteUrl($url, $scheme, $host, $port, $ip->toString());
    }
}
