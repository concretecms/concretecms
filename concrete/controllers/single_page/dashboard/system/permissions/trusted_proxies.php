<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Permissions;

use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;
use IPLib\Address\AddressInterface;
use IPLib\Factory;
use IPLib\ParseStringFlag;
use IPLib\Range\Pattern;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class TrustedProxies extends DashboardPageController
{
    /**
     * @var string
     */
    const HEADERNAME_FORWARDED = 'FORWARDED';

    /**
     * @var string
     */
    const HEADERNAME_X_FORWARDED_FOR = 'X_FORWARDED_FOR';

    /**
     * @var string
     */
    const HEADERNAME_X_FORWARDED_HOST = 'X_FORWARDED_HOST';

    /**
     * @var string
     */
    const HEADERNAME_X_FORWARDED_PROTO = 'X_FORWARDED_PROTO';

    /**
     * @var string
     */
    const HEADERNAME_X_FORWARDED_PORT = 'X_FORWARDED_PORT';

    public function view()
    {
        $config = $this->app->make('config');
        $trustedIPs = $config->get('concrete.security.trusted_proxies.ips');
        if (!is_array($trustedIPs)) {
            $trustedIPs = [];
        }
        $this->set('trustedIPs', $trustedIPs);
        $this->set('trustableHeaders', $this->getTrustableHeaderNames());
        $this->set('trustedHeaders', $this->getTrustedHeaderNames());
        $this->set('requestForwardedHeaders', $this->getRequestForwardedHeaders());
        $this->set('request', $this->request);
        $currentProxyIP = null;
        if ($this->request->isFromTrustedProxy()) {
            $clientIP = $this->app->make(AddressInterface::class);
            $rawClientIP = Factory::parseAddressString($this->request->server->get('REMOTE_ADDR'), ParseStringFlag::IPV4_MAYBE_NON_DECIMAL | ParseStringFlag::IPV4ADDRESS_MAYBE_NON_QUAD_DOTTED | ParseStringFlag::MAY_INCLUDE_PORT | ParseStringFlag::MAY_INCLUDE_ZONEID);
            if ((string) $clientIP !== (string) $rawClientIP) {
                $currentProxyIP = $rawClientIP;
            }
        }
        $this->set('currentProxyIP', $currentProxyIP);
    }

    public function save()
    {
        if (!$this->token->validate('ccm_trusted_proxies_save')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $post = $this->request->request;
        list($validIPs, $invalidIPs) = $this->parseIPList($post->get('trustedIPs'));
        $numInvalid = count($invalidIPs);
        if ($numInvalid > 0) {
            $this->error->add(t2('This IP address is not valid: %2$s', 'These IP addresses are not valid: %2$s', $numInvalid, "\n- " . implode("\n- ", $invalidIPs)));
        }
        $trustedHeaderFlags = 0;
        $trustedHeaderNames = $post->get('trustedHeaders');
        if (is_array($trustedHeaderNames)) {
            $map = $this->getSymfonyHeadersMap();
            foreach ($trustedHeaderNames as $trustedHeaderName) {
                $flag = array_search($trustedHeaderName, $map, true);
                if ($flag !== false) {
                    $trustedHeaderFlags |= (int) $flag;
                }
            }
        }

        if ($this->error->has()) {
            $this->view();

            return;
        }
        $config = $this->app->make('config');
        $config->save('concrete.security.trusted_proxies.ips', $validIPs);
        $config->save('concrete.security.trusted_proxies.headers', $trustedHeaderFlags);
        $this->flash('success', t('The configuration has been updated.'));

        return $this->app->make(ResponseFactoryInterface::class)->redirect(
            $this->action(''),
            302
        );
    }

    /**
     * @param string|mixed $input
     *
     * @return string[][]
     */
    protected function parseIPList($input)
    {
        $validIPs = [];
        $invalidIPs = [];
        if (is_string($input)) {
            foreach (preg_split('/\s+/', $input, -1, PREG_SPLIT_NO_EMPTY) as $rawRange) {
                if (!in_array($rawRange, $invalidIPs, true)) {
                    $range = Factory::parseRangeString($rawRange);
                    if ($range === null || $range instanceof Pattern) {
                        $invalidIPs[] = $rawRange;
                    } else {
                        $range = (string) $range;
                        if (!in_array($range, $validIPs, true)) {
                            $validIPs[] = $range;
                        }
                    }
                }
            }
        }

        return [$validIPs, $invalidIPs];
    }

    /**
     * Get the list of headers that can be marked as trusted.
     *
     * @return string[]
     */
    protected function getTrustableHeaderNames()
    {
        return [
            static::HEADERNAME_FORWARDED,
            static::HEADERNAME_X_FORWARDED_FOR,
            static::HEADERNAME_X_FORWARDED_HOST,
            static::HEADERNAME_X_FORWARDED_PROTO,
            static::HEADERNAME_X_FORWARDED_PORT,
        ];
    }

    /**
     * Get the map from the Symfony header bits to the header names.
     *
     * @return array
     */
    protected function getSymfonyHeadersMap()
    {
        return [
            SymfonyRequest::HEADER_FORWARDED => static::HEADERNAME_FORWARDED,
            SymfonyRequest::HEADER_X_FORWARDED_FOR => static::HEADERNAME_X_FORWARDED_FOR,
            SymfonyRequest::HEADER_X_FORWARDED_HOST => static::HEADERNAME_X_FORWARDED_HOST,
            SymfonyRequest::HEADER_X_FORWARDED_PROTO => static::HEADERNAME_X_FORWARDED_PROTO,
            SymfonyRequest::HEADER_X_FORWARDED_PORT => static::HEADERNAME_X_FORWARDED_PORT,
        ];
    }

    /**
     * Get the map from the Symfony header bits to the header names.
     *
     * @return array
     */
    protected function getLegacySymfonyHeadersMap()
    {
        return [
            'forwarded' => static::HEADERNAME_FORWARDED,
            'client_ip' => static::HEADERNAME_X_FORWARDED_FOR,
            'client_host' => static::HEADERNAME_X_FORWARDED_HOST,
            'client_proto' => static::HEADERNAME_X_FORWARDED_PROTO,
            'client_port' => static::HEADERNAME_X_FORWARDED_PORT,
        ];
    }

    /**
     * Get the currently configured trusted header names.
     *
     * @return string[]
     */
    protected function getTrustedHeaderNames()
    {
        $result = [];
        $headers = $this->app->make('config')->get('concrete.security.trusted_proxies.headers');
        if (is_array($headers)) {
            $map = $this->getLegacySymfonyHeadersMap();
            foreach ($map as $legacyHeaderName => $headerName) {
                if (in_array($legacyHeaderName, $headers, true)) {
                    $result[] = $headerName;
                }
            }
        } else {
            $flags = (int) $headers;
            $map = $this->getSymfonyHeadersMap();
            foreach ($map as $headerFlag => $headerName) {
                if ((int) $headerFlag & $flags) {
                    $result[] = $headerName;
                }
            }
        }

        return $result;
    }

    /**
     * Extract the prospective forwarded header names and their values.
     *
     * @return array
     */
    protected function getRequestForwardedHeaders()
    {
        $result = [];
        $server = $this->request->server;
        foreach ($this->getTrustableHeaderNames() as $name) {
            if ($server->has("HTTP_{$name}")) {
                $result[$name] = $server->get("HTTP_{$name}");
            }
        }

        return $result;
    }
}
