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

        $ipFlags = IPParseStringFlag::IPV4_MAYBE_NON_DECIMAL | IPParseStringFlag::IPV4ADDRESS_MAYBE_NON_QUAD_DOTTED | IPParseStringFlag::MAY_INCLUDE_PORT | IPParseStringFlag::MAY_INCLUDE_ZONEID;
        $ip = IPFactory::parseAddressString($host, $ipFlags);
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

        $port = $parsedUrl->getPort() ?: ($scheme === 'http' ? 80 : 443);

        return new ValidatedRemoteUrl($url, $scheme, $host, (int) $port, $ip->toString());
    }
}
