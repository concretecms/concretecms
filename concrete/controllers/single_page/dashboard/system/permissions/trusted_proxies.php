<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Permissions;

use Concrete\Core\Page\Controller\DashboardPageController;
use IPLib\Factory;
use IPLib\Range\Pattern;

class TrustedProxies extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make('config');
        $trustedIPs = $config->get('concrete.security.trusted_proxies.ips');
        if (!is_array($trustedIPs)) {
            $trustedIPs = [];
        }
        $this->set('trustedIPs', $trustedIPs);
    }

    public function save()
    {
        if ($this->token->validate('ccm_trusted_proxies_save')) {
            $post = $this->request->request;
            $this->parseIPList($post->get('trustedIPs'), $validIPs, $invalidIPs);
            $numInvalid = count($invalidIPs);
            if ($numInvalid > 0) {
                $this->error->add(t2('This IP address is not valid: %2$s', 'These IP addresses are not valid: %2$s', $numInvalid, "\n- " . implode("\n- ", $invalidIPs)));
            }
            if (!$this->error->has()) {
                $config = $this->app->make('config');
                $config->save('concrete.security.trusted_proxies.ips', $validIPs);
                $this->flash('success', t('The trusted proxies configuration has been updated.'));
                $this->redirect($this->action(''));
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
        $this->view();
    }

    /**
     * @param string $input
     * @param string[] $validIPs
     * @param string[] $invalidIPs
     */
    private function parseIPList($input, &$validIPs, &$invalidIPs)
    {
        $validIPs = [];
        $invalidIPs = [];
        if (is_string($input)) {
            if (preg_match_all('/\S+/', $input, $matches)) {
                foreach ($matches[0] as $rawRange) {
                    if (!in_array($rawRange, $invalidIPs, true)) {
                        $range = Factory::rangeFromString($rawRange);
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
        }
    }
}
